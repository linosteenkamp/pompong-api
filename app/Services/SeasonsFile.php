<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/07
 * Time: 5:32 PM
 */

namespace pompong\Services;


class SeasonsFile
{

    public function get($data, $user) {
        $seasons = $this->getSeasons($data);
        Return $this->createDownloadFile($seasons, $user['id']);
    }

    private function getSeasons($data) {
        $tmpData = Array();
        foreach ($data as $season) {
            if ($season->location != '') {
                array_push($tmpData, substr(dirname($season->location),12));
            }
        }

        sort($tmpData);

        return array_unique($tmpData);
    }

    Private function createDownloadFile($data, $userId) {
        $tmpFileName = "files/" . $userId . "-" . date("Y-m-d-H-i-s") . '.txt';

        $metaFile = fopen($tmpFileName, "w+");
        foreach ($data as $line) {
            preg_match('/(^.+)(?=\/)/', $line, $matches);
            fwrite($metaFile, 'test -d "' . $matches[0] . '" || mkdir "' . $matches[0] .
                '" &&  rsync -vadr --exclude-from="$1/Exclude.txt" --delete-excluded ' .
                '"$1/' . $line . '" "$2/' . $matches[0] . '/"' . PHP_EOL);
        }
        fclose($metaFile);

        return ['file_name' => $tmpFileName];
    }
}
