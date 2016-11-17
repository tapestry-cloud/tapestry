<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
                new InputOption('rollback', 'r', InputOption::VALUE_NONE, 'Revert to an older installation of tapestry'),
                new InputOption('clean-backups', null, InputOption::VALUE_NONE, 'Delete old backups during an update. This makes the current version of tapestry the only backup available after the update'),
            ]);
    }

    protected function fire()
    {
        $localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        if (!$this->input->getOption('test') && pathinfo($localFilename, PATHINFO_EXTENSION) !== 'phar'){
            $this->output->writeln('[!] Self-Update only works on phar archives.');
            exit(1);
        }

        $tempDirectory = dirname($localFilename) . DIRECTORY_SEPARATOR . 'tmp';
        $tempFilename = dirname($localFilename) . DIRECTORY_SEPARATOR . basename($localFilename, '.phar').'-temp.phar';

        if (!file_exists($tempDirectory)){
            mkdir($tempDirectory);
        }

        $jsonPathName = $tempDirectory . DIRECTORY_SEPARATOR . 'release.json';
        $this->downloadFile($this->releaseApiUrl, $jsonPathName);

        $releaseJson = json_decode(file_get_contents($jsonPathName));

        $latestVersion = $releaseJson->tag_name;
        $latestVersionDownloadUrl = $releaseJson->assets[0]->browser_download_url;

        dd($releaseJson);
        return 0;
    }

    private function downloadFile($url, $filepath){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Tapestry CLI Update');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept' => 'application/vnd.github.v3+json'
        ]);
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