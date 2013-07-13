<?php

namespace Fuel\Tasks;

/**
 *
 * Code Completion Code Generator
 *
 */
class Ccc
{

    private static $without_dirs;
    private static $generated_code = array();
    private static $dup_class = array();

    public static function run()
    {
        // keep without-genereted directory names
        if (\Cli::option('without', false) or \Cli::option('WITHOUT', false))
        {
            self::$without_dirs = explode('+', \Cli::option('without'));
        }
        else
        {
            self::$without_dirs = false;
        }

        // other command line handling to generate
        if (\Cli::option('all', false) or \Cli::option('ALL', false))
        {
            $dir = \File::read_dir(COREPATH, 0, 'php');
            self::generate_redefine($dir, '', COREPATH);
            $dir = \File::read_dir(APPPATH, 0, 'php');
            self::generate_redefine($dir, '', APPPATH);
            $dir = \File::read_dir(PKGPATH, 0, 'php');
            self::generate_redefine($dir, '', PKGPATH);
        }
        else
        {
            if (\Cli::option('core', false) or \Cli::option('CORE', false))
            {
                $dir = \File::read_dir(COREPATH, 0, 'php');
                self::generate_redefine($dir, '', COREPATH);
            }
            elseif (\Cli::option('app', false) or \Cli::option('APP', false))
            {
                $dir = \File::read_dir(APPPATH, 0, 'php');
                self::generate_redefine($dir, '', APPPATH);
            }
            elseif (\Cli::option('packages', false) or \Cli::option('PACKAGES', false))
            {
                $dir = \File::read_dir(PKGPATH, 0, 'php');
                self::generate_redefine($dir, '', PKGPATH);
            }
            else
            {
                echo PHP_EOL.'usage: [php] oil [-all] [-core] [-app] [-packages] [-without=directory_name[+directory_name]...] [-help]'.PHP_EOL;
                echo 'Code Completion Code Generator for FuelPHP'.PHP_EOL.PHP_EOL;
                echo 'php at head of commnad line needed if you use oil in cmd.exe on windows normaly.'.PHP_EOL;
                echo '-all : Generate from app, core and packages directory.'.PHP_EOL;
                echo '-app : Generate from app directory.'.PHP_EOL;
                echo '-core : Generate from core directory.'.PHP_EOL;
                echo '-help : Display this help and exit.'.PHP_EOL;
                echo '-packages : Generate from core packages.'.PHP_EOL;
                echo '-without : + seperated directory name where no generated code.'.PHP_EOL.PHP_EOL;
                echo 'This task output result into standard output.'.PHP_EOL;
                echo 'So after you checked output code, redirect to your output PHP file. Do like :'.PHP_EOL;
                echo 'php oil r ccc -all -without=tasks+migrations+vendor+tests+oil+orm+parser > CodeCompetionCode.php'.PHP_EOL;
                echo 'And put the file on anywhere your source code folder.'.PHP_EOL.PHP_EOL;
                echo 'Caution:'.PHP_EOL;
                echo 'This generated code work on no namespace used on your code in IDE editor.'.PHP_EOL;
                echo 'Because this just redefine every short class name as general(top) level of namespace.'.PHP_EOL;
                echo 'So never work at a code like a task and/or a package, that used namespace.(Or your code used namespace. ;) )'.PHP_EOL;
                echo 'If you want to work code completion on code used namespace, it is the better way switch comment off for the namespace statement during you edit it.';
                exit(0);
            }
        }

        ksort(self::$generated_code);
        ksort(self::$dup_class);

        echo '<?php'.PHP_EOL;
        if (!empty(self::$dup_class))
        {
            echo '// Duplicated classes from here'.PHP_EOL;
            echo implode("\n", self::$dup_class);
            echo PHP_EOL.'// Duplicated classes to here'.PHP_EOL;
        }
        echo implode("\n", self::$generated_code);
    }

    private static function generate_redefine($ary, $path, $base)
    {
        foreach ($ary as $key => $val)
        {
            if (is_array($val))
            {

                if ((self::$without_dirs and !in_array(trim($key, '/\\'), self::$without_dirs)) or !self::$without_dirs)
                {
                    self::generate_redefine($val, $path.$key, $base);
                }
            }
            else
            {
                // read php code
                try
                {
                    $buff = explode("\n", \File::read($base.$path.$val, true));
                }
                catch (\Fuel\Core\InvalidPathException $e)
                {
                    die('Failed to read file : '.$path.$val);
                }

                $namespace = '';

                foreach ($buff as $i)
                {
                    // get namespace
                    if (preg_match('/^[ \\t]*namespace[ \\t]+(.+?)[ \\t]*;/', $i, $matched) === 1)
                    {
                        $namespace = $matched[1];
                    }
                    // get abstract class
                    elseif ($namespace != '' and preg_match('/^[ \\t]*abstract[ \\t]+class[ \\t]+(\\w+)/', $i, $matched) === 1)
                    {
                        self::reg_classname($matched[1],
                            "abstract class\t".$matched[1].' extends '.$namespace.'\\'.$matched[1].'{};');
                    }
                    // get interface
                    elseif ($namespace != '' and preg_match('/^[ \\t]*interface[ \\t]+(\\w+)/', $i, $matched) === 1)
                    {
                        self::reg_classname($matched[1],
                            "interface\t\t".$matched[1].' extends '.$namespace.'\\'.$matched[1].'{};');
                    }
                    // get class
                    elseif ($namespace != '' and preg_match('/^[ \\t]*class[ \\t]+(\\w+)/', $i, $matched))
                    {
                        self::reg_classname($matched[1],
                            "class\t\t\t".$matched[1].' extends '.$namespace.'\\'.$matched[1].'{};');
                    }
                }
            }
        }
    }

    private static function reg_classname($key, $str)
    {
        if (key_exists($key, self::$generated_code))
        {
            self::$dup_class[$key] = $str;
        }
        else
        {
            self::$generated_code[$key] = $str;
        }
    }

}
