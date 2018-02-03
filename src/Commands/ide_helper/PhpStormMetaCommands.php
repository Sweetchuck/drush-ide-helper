<?php

namespace Drush\Commands\ide_helper;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\ide_helper\Utils;
use Drupal\ide_helper\Robo\IdeHelperPhpstormMetaTaskLoader;
use Robo\State\Data as RoboStateData;
use Robo\Tasks;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;

class PhpStormMetaCommands extends Tasks {

  use IdeHelperPhpstormMetaTaskLoader;

  /**
   * @var int
   */
  const EXIT_CODE_OUTPUT_DIR_DETECTION = 1;

  /**
   * @var int
   */
  const EXIT_CODE_OUTPUT_DIR_NOT_EXISTS = 2;

  /**
   * @var int
   */
  const EXIT_CODE_OUTPUT_DIR_NOT_DIR = 3;

  /**
   * @hook init ide-helper:phpstorm-meta
   */
  public function ideHelperPhpstormMetaHookInit(InputInterface $input) {
    $outputDirOptionName = 'outputDir';
    if ($input->hasOption($outputDirOptionName)) {
      $outputDir = $input->getOption($outputDirOptionName);
      if ($outputDir === NULL) {
        $cwd = $this
          ->getContainer()
          ->get('config')
          ->get('env.cwd');

        $input->setOption($outputDirOptionName, Utils::autodetectIdeaProjectRoot($cwd));
      }
    }
  }

  /**
   * @hook validate ide-helper:phpstorm-meta
   */
  public function ideHelperPhpstormMetaHookValidate(CommandData $commandData): void {
    $outputDirOptionName = 'outputDir';
    $input = $commandData->input();
    if ($input->hasOption($outputDirOptionName)) {
      $outputDir = $input->getOption($outputDirOptionName);
      if ($outputDir === NULL) {
        $cwd = $this
          ->getContainer()
          ->get('config')
          ->get('env.cwd');

        $outputDir = Utils::autodetectIdeaProjectRoot($cwd);
        if (!$outputDir) {
          throw new \InvalidArgumentException(
            dt("The output directory cannot be detected automatically. Current directory: '$cwd'"),
            static::EXIT_CODE_OUTPUT_DIR_DETECTION
          );
        }

        $input->setOption($outputDirOptionName, $outputDir);
      }
      elseif (!file_exists($outputDir)) {
        throw new \InvalidArgumentException(
          dt(
            "The given path '@path' is not exists.",
            [
              '@path' => $outputDir,
            ]
          ),
          static::EXIT_CODE_OUTPUT_DIR_NOT_EXISTS
        );
      }
      elseif (!is_dir($outputDir)) {
        throw new \InvalidArgumentException(
          dt(
            "The given path '@path' cannot be used as output directory, because it is exists but not a directory.",
            [
              '@path' => $outputDir,
            ]
          ),
          static::EXIT_CODE_OUTPUT_DIR_NOT_DIR
        );
      }
    }
  }

  /**
   * Generate .phpstorm.meta.php file.
   *
   * @command ide-helper:phpstorm-meta
   * @bootstrap full
   */
  public function ideHelperPhpstormMeta(
    array $options = [
      'outputDir' => NULL,
      'multipleFiles' => TRUE,
    ]
  ) {
    $drupalContainer = \Drupal::getContainer();

    $collectorTask = $this->taskIdeHelperPhpstormMetaCollector();
    $collectorTask->setDrupalRoot(DRUPAL_ROOT);
    $collectorTask->setContainer($drupalContainer);

    $rendererTask = $this->taskIdeHelperPhpstormMetaRenderer();
    $rendererTask->setContainer($drupalContainer);
    $rendererTask->setMultipleFiles($options['multipleFiles']);
    $rendererTask->deferTaskConfiguration('setPhpStormMeta', 'phpStormMeta');

    $cwd = $this
      ->getContainer()
      ->get('config')
      ->get('env.cwd');

    return $this
      ->collectionBuilder()
      ->addTask($collectorTask)
      ->addTask($rendererTask)
      ->addTask($this->taskFilesystemStack()->remove($options['outputDir'] . '/.phpstorm.meta.php'))
      ->addCode(function (RoboStateData $data) use ($options, $cwd) : int {
        if (empty($data['phpStormMetaFiles'])) {
          return 0;
        }

        try {
          $relativeOutputDir = $cwd === $options['outputDir'] ?
            '.'
            : Path::makeRelative($options['outputDir'], $cwd);
        }
        catch (\Exception $e) {
          $relativeOutputDir = NULL;
        }
        $relativeOutputDir = $relativeOutputDir ?: $options['outputDir'];

        $output = $this->output();
        $errorOutput = $output;
        if ($output instanceof ConsoleOutput) {
          $errorOutput = $output->getErrorOutput();
        }

        $errorOutput->writeln(
          "Base directory: '<info>{$cwd}</info>'",
          OutputInterface::VERBOSITY_VERBOSE
        );

        $errorOutput->writeln(
          "Output directory: '<info>{$options['outputDir']}</info>'",
          OutputInterface::VERBOSITY_VERBOSE
        );

        $errorOutput->writeln(
          "Relative output directory: '<info>{$relativeOutputDir}</info>'",
          OutputInterface::VERBOSITY_VERBOSE
        );

        foreach ($data['phpStormMetaFiles'] as $fileName => $fileContent) {
          $this
            ->taskWriteToFile("{$relativeOutputDir}/$fileName")
            ->text($fileContent)
            ->run()
            ->stopOnFail();
        }

        return 0;
      });
  }

}
