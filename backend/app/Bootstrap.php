<?php

namespace App;

use Lapis\App\Kernel;
use Lapis\DI\Configurator;
use Lapis\Dirs;
use Lapis\Utils\DotEnv\DotEnv;
use Nette\Utils\Finder;
use function file_exists;
use function file_put_contents;

final class Bootstrap
{
  private const string ConfigsFile = Dirs::Temp . '/configs.php';


  public static function boot(): Kernel
  {
    $configurator = new Configurator();

    /*
     * Debug cookie can be set using JS console in DevTools and calls:
     * document.cookie = 'debugCookie=...; expires=Mon, 31 Dec 2100 20:47:11 UTC; path=/'
     */
    $env = new DotEnv(Dirs::Root);
    $configurator->setDebugMode($env->DEBUG_COOKIE === ($_COOKIE['debugCookie'] ?? ''));
    $configurator->enableTracy(Dirs::Logs);
    $configurator->setTempDirectory(Dirs::Temp);


    if (!file_exists(self::ConfigsFile) || $configurator->isDebugMode()) {
      $files = [];

      // configs from modules
      $configs = Finder::findFiles(Dirs::App . '/**/*.neon');

      foreach ($configs as $file) {
        $files[] = $file;
      }

      // some extra configs
      // $files[] = ...

      // for better performance generating file with set config files
      $phpCode = "<?php\n";

      foreach ($files as $file) {
        $file = realpath($file);
        $phpCode .= "\$configurator->addConfig('{$file}');\n";
      }

      file_put_contents('nette.safe://' . self::ConfigsFile, $phpCode);
    }

    require self::ConfigsFile;

    return $configurator->createContainer()->getByType(Kernel::class);
  }
}