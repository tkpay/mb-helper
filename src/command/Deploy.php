<?php

namespace mb\helper\command;

use mb\helper\File;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Env;

class Deploy extends Command
{
    protected function configure()
    {
        $this->setName('deploy')->setDescription('Deploy extension');
        $this->addOption('--all', null, Option::VALUE_NONE, 'Deploy all extensions');
        $this->addOption('--public', null, Option::VALUE_NONE, 'Deploy public static files');
        $this->addOption('--extension', '-e', Option::VALUE_REQUIRED, 'Deploy one extension');
    }

    protected function execute(Input $input, Output $output)
    {
        $options = $input->getOptions();
        if ($options['all']) {
            //全部
            $extPath = Env::get('EXTEND_PATH') . '*';
            $files = glob($extPath);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $extensionName = pathinfo($file, PATHINFO_FILENAME);
                    $this->deployExtension($extensionName);
                }
            }
            $this->output->writeln("\nall done");

            return;
        }
        if ($options['extension']) {
            //部分
            $this->deployExtension($options['extension']);

            return;
        }
        if ($options['public']) {
            $this->deployPublic();

            return;
        }
        $output->write("Usage:\n\nthink deploy --all\nthink deploy --public\nthink deploy --extension=[extension-name]\nthink deploy -e [extension-name]\n");
    }

    private function deployExtension($extensionName)
    {
        $extPath = Env::get('EXTEND_PATH') . $extensionName;
        if (!is_dir($extPath)) {
            $this->output->writeln('extension dir not exists');

            return;
        }
        $this->output->writeln("deploy extension: {$extensionName}");

        //处理static
        $staticSrcPath = $extPath . '/static';
        if (is_dir($staticSrcPath)) {
            $this->output->writeln('processing static files ... ');
            $staticDesPath = Env::get('ROOT_PATH') . "public/static/{$extensionName}";
            File::rmdirs($staticDesPath);
            File::copyRecurse($staticSrcPath, $staticDesPath, true);
        }

        $this->output->writeln("deploy done: {$extensionName}");
    }

    private function deployPublic()
    {
        //bench资源部署
        $staticDesPath = Env::get('ROOT_PATH') . 'public/static/bench';
        $staticSrcPath = Env::get('VENDOR_PATH') . 'almasaeed2010/adminlte';
        
        //adminlte
        $set = array('bower_components', 'dist', 'plugins');
        foreach ($set as $item) {
            $this->output->writeln("processing AdminLTE files ... ($item)");
            File::rmdirs("{$staticDesPath}/{$item}");
            File::copyRecurse("{$staticSrcPath}/{$item}", "{$staticDesPath}/{$item}");
        }
    }
}