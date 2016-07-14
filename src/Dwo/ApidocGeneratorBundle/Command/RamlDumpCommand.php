<?php

namespace Dwo\ApidocGeneratorBundle\Command;

use Dwo\ApidocGenerator\ApidocGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RamlDumpCommand
 *
 * @author Dave Www <davewwwo@gmail.com>
 */
class RamlDumpCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected $logDir;

    /**
     * @var ApidocGenerator
     */
    protected $generator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('dwo:apidoc_generator:dump')
            #->addArgument('key', InputArgument::OPTIONAL, date("Ymd"), date("Ymd"))
            ->addOption('raml', null, InputOption::VALUE_NONE)
            ->addOption('swagger', null, InputOption::VALUE_NONE)
            ->addOption('html', null, InputOption::VALUE_NONE)
            ->addOption('json', null, InputOption::VALUE_NONE)
            ->setDescription('Dumps BehatLogs');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '4G');

        $this->logDir = $this->getContainer()->get('kernel')->getLogDir();
        $this->generator = $this->getContainer()->get('dwo.apidoc_generator');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getOption('raml') ? 'raml' : 'swagger';
        $dumpOption = $input->getOption('json') ? 'json' : 'yml';
        $renderOption = $input->getOption('html') ? 'html' : false;

        /**
         * generate
         */
        $tree = $this->generator->generate($type);
        $dump = $this->generator->dump($tree, $dumpOption);

        $output->writeln($dump);
        
        /**
         * render
         */
        if ('raml' === $type || 'html' === $renderOption) {
            if ('yml' !== $dumpOption) {
                throw new \Exception('raml html render need a yml dump');
            }

            $content = '#%RAML 0.8'.PHP_EOL.$dump;
            file_put_contents($ramlPath = $this->logDir.'/raml.raml', $content);
            $output->writeln('<info>RAML file dumped to: '.$ramlPath.'</info>');

            if ($input->getOption('html')) {
                $htmlPath = $this->logDir.'/api_doc.html';
                exec(sprintf('raml2html %s > %s', $ramlPath, $htmlPath));

                $output->writeln('<info>HTML file dumped to: '.$htmlPath.'</info>');
            }
        }
    }

}