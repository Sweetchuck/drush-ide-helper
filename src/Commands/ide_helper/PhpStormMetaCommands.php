<?php

namespace Drush\Commands\ide_helper;

use Consolidation\AnnotatedCommand\AnnotationData;
use Consolidation\AnnotatedCommand\CommandData;
use Drupal\ide_helper\Utils;
use Drupal\ide_helper\Robo\IdeHelperPhpstormMetaTaskLoader;
use Drush\Symfony\DrushArgvInput;
use Robo\State\Data as RoboStateData;
use Robo\Tasks;
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
  public function ideHelperPhpstormMetaHookInit(DrushArgvInput $input, AnnotationData $annotationData) {
    $outputDirOptionName = 'outputDir';
    if ($input->hasOption($outputDirOptionName)) {
      $outputDir = $input->getOption($outputDirOptionName);
      if (!$outputDir) {
        $input->setOption($outputDirOptionName, Utils::autodetectIdeaProjectRoot(getcwd()));
      }
    }
  }

  /**
   * @hook validate ide-helper:phpstorm-meta
   */
  public function ideHelperPhpstormMetaHookValidate(CommandData $commandData): void {
    $outputDirOptionName = 'outputDir';
    if ($commandData->input()->hasOption($outputDirOptionName)) {
      $outputDir = $commandData->input()->getOption($outputDirOptionName);
      if (!$outputDir) {
        $outputDir = Utils::autodetectIdeaProjectRoot(getcwd());
        if (!$outputDir) {
          throw new \InvalidArgumentException(
            dt('The output directory cannot be detected automatically.'),
            static::EXIT_CODE_OUTPUT_DIR_DETECTION
          );
        }
      }

      if (!file_exists($outputDir)) {
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

      if (!is_dir($outputDir)) {
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
      'outputDir' => '',
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

    return $this
      ->collectionBuilder()
      ->addTask($collectorTask)
      ->addTask($rendererTask)
      ->addTask($this->taskFilesystemStack()->remove($options['outputDir'] . '/.phpstorm.meta.php'))
      ->addCode(function (RoboStateData $data) use ($options) : int {
        if (empty($data['phpStormMetaFiles'])) {
          return 0;
        }

        $cwd = getcwd();
        $relativeOutputDir = $options['outputDir'];
        try {
          $relativeOutputDir = Path::makeRelative($options['outputDir'], $cwd);
        }
        catch (\Exception $e) {
          // Nothing to do.
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
