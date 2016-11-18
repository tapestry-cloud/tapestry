<?php namespace Tapestry\Console\Commands;

use Composer\Semver\Comparator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tapestry\Tapestry;
use ZipArchive;

class SelfUpdateCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var string
     */
    private $releaseApiUrl = 'https://api.github.com/repos/carbontwelve/tapestry/releases/latest';

    private $currentPharFileName;

    private $scratchDirectoryPath;

    private $pharExists = false;

    /**
     * InitCommand constructor.
     * @param Filesystem $filesystem
     * @param Finder $finder
     */
    public function __construct(Filesystem $filesystem, Finder $finder)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->currentPharFileName = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        $this->scratchDirectoryPath = dirname($this->currentPharFileName) . DIRECTORY_SEPARATOR . 'tmp';
        $this->pharExists = (pathinfo($this->currentPharFileName, PATHINFO_EXTENSION) === 'phar');

        if (!$filesystem->exists($this->scratchDirectoryPath)){
            $filesystem->mkdir($this->scratchDirectoryPath);
        }
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('self-update')
            ->setDescription('Update your installed version of Tapestry.')
            ->setDefinition([
                new InputOption('test', 't', InputOption::VALUE_NONE, 'Test functionality outside of phar'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'Force update even if you have the latest version'),
                new InputOption('rollback', 'r', InputOption::VALUE_NONE, 'Revert to an older installation of tapestry'),
                new InputOption('clean-backups', null, InputOption::VALUE_NONE, 'Delete old backups during an update. This makes the current version of tapestry the only backup available after the update'),
            ]);
    }

    protected function fire()
    {
        if (!$this->input->getOption('test') && $this->pharExists === false){
            $this->output->writeln('[!] Self-Update only works on phar archives.');
            exit(1);
        }

        $jsonPathName = $this->scratchDirectoryPath . DIRECTORY_SEPARATOR . 'release.json';
        if (!$this->downloadFile($this->releaseApiUrl, $jsonPathName, ['Accept' => 'application/vnd.github.v3+json'])) {
            $this->panic('There was a problem in downloading the update, please try again.');
        }

        $releaseJson = json_decode(file_get_contents($jsonPathName));
        $latestVersion = $releaseJson->tag_name;

        if ($this->input->getOption('force') === false && Comparator::greaterThanOrEqualTo(Tapestry::VERSION, $latestVersion)){
            $this->output->writeln('You already have the latest version of Tapestry ['. Tapestry::VERSION .']. Doing nothing and exiting.');
            exit();
        }

        $this->backupPhar();
        $this->replacePhar($releaseJson->assets[0]->browser_download_url);

        return 0;
    }

    private function backupPhar()
    {
        if ($this->input->getOption('test') === true && $this->pharExists === false){
            $this->output->writeln('[*] Pretending to Backup Phar');
            return;
        }elseif($this->input->getOption('test') === false && $this->pharExists === false){
            $this->panic('Phar Archive Not Found!');
        }

        $this->output->writeln('[*] Making Backup Phar');
        $tempFilename = dirname($this->currentPharFileName) . DIRECTORY_SEPARATOR . basename($this->currentPharFileName, '.phar').'-temp.phar';
        $this->filesystem->copy($this->currentPharFileName, $tempFilename);
    }

    private function replacePhar($latestVersionDownloadUrl)
    {
        $this->output->writeln('[*] Downloading Update');
        $downloadToPath = $this->scratchDirectoryPath . DIRECTORY_SEPARATOR . pathinfo($latestVersionDownloadUrl, PATHINFO_BASENAME);
        if (!$this->downloadFile($latestVersionDownloadUrl, $downloadToPath)){
            $this->panic('There was a problem in downloading the update, please try again.');
        }

        $this->output->writeln('[*] Unpacking Update');
        $this->unzip($downloadToPath, dirname($this->currentPharFileName));
    }

    private function unzip($from, $to){
        $zip = new ZipArchive;
        $res = $zip->open($from);
        if ($res === true) {
            $zip->extractTo($to);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    private function downloadFile($url, $filepath, $accept = null){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Tapestry CLI Update');
        if (! is_null($accept)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $accept);
        }
        $raw_file_data = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if(curl_errno($ch)){
            $this->error(curl_error($ch));
            curl_close($ch);
            exit(1);
        }

        if ($responseCode !== 200) {
            $this->error('Github responded with response code ['. $responseCode .']');
            curl_close($ch);
            exit(1);
        }

        curl_close($ch);

        file_put_contents($filepath, $raw_file_data);
        return (filesize($filepath) > 0)? true : false;
    }
}