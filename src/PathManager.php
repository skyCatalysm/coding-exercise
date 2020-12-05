<?php

declare(strict_types=1);

class PathManager
{
    private static $paths = [];

    public static function setPaths(array $data = [])
    {
        self::$paths = $data;
    }

    /**
     * Parses and converts each path into an array
     *
     * @param array $paths the list of paths
     * @return array list of array converted paths
     */
    public static function pathsToArray(): array
    {
        $paths = self::$paths;
        $pathsArray = [];
        $explodedPaths = array_map(function ($path) {
            return explode("/", $path);
        }, $paths);

        sort($explodedPaths);
        foreach ($explodedPaths as $path) {
            $temporaryArray = null;
            foreach (array_reverse($path) as $file) {
                if (empty($file)) {
                    break;
                }
                $temporaryArray = $temporaryArray ? [$file => $temporaryArray] : [$file];
            }
            $pathsArray[] = $temporaryArray;
        }

        return $pathsArray;
    }

    /**
     * Converts the output of pathsToArray to a tree array
     *
     * @param array $paths
     * @return array $pathsTreeArray
     */
    public static function pathsArrayToTree(): array
    {
        $pathsArray = self::pathsToArray();
        $pathsTreeArray = array_merge_recursive(...$pathsArray);

        return $pathsTreeArray;
    }

    /**
     * Recursively prints the tree array
     *
     * @param integer $maxDepth
     * @param integer $maxLeaf
     * @return void
     */
    public static function printTreeArray($maxDepth = 0, $maxLeaf = 0): void
    {
        $pathsTreeArray = self::pathsArrayToTree();

        $recursivePrint = function ($p, $mD = 0, $mL = 0, $sP = 0) use (&$recursivePrint) {

            if (!is_array($p)) {
                return;
            }

            $leafCounter = 0;
            foreach ($p as $key => $value) {
                $isBranch = is_array($value);
                if (!$isBranch && ($leafCounter >= $mL)) {
                    continue;
                }
                if ($mD === 0 && $isBranch) {
                    return;
                }
                for ($i = 0; $i < $sP; $i++) {
                    echo " ";
                }
                if ($isBranch) {
                    echo $key . "\n";
                } else {
                    echo $value . "\n";
                    $leafCounter++;
                }

                $recursivePrint($value, $mD - 1, $mL, $sP + 4);
            }
        };
        $recursivePrint($pathsTreeArray, $maxDepth, $maxLeaf, 0);
    }

    /**
     * Randomly generates file paths
     *
     * @param string $basePath
     * @param integer $pathsReturned
     * @param integer $maxDepth
     * @param integer $maxFolderFiles
     * @return array
     */
    public static function randomFileGenerator(
        string $basePath,
        int $pathsReturned,
        int $maxDepth,
        int $maxFolderFiles
    ): array {
        $generatedPaths = [];
        $arrayCountChecker = ['counter' => 0];
        while (true) {
            //Break if path is complete or if the maxed leaf per directory is achieved
            $isPathComplete = count($generatedPaths) >= $pathsReturned;
            $hasMaxedDirectoryLeaf = $arrayCountChecker['counter'] >= ($maxDepth * $maxFolderFiles);
            if ($isPathComplete || $hasMaxedDirectoryLeaf) {
                break;
            }

            //Randomize depth path
            $randDepth = rand(1, $maxDepth);
            $folderPath = "";
            for ($x = 1; $x <= $randDepth; $x++) {
                $folderPath .= "/folder" . $x;
            }
            if (!array_key_exists($folderPath, $arrayCountChecker)) {
                $arrayCountChecker[$folderPath] = 0;
            }
            //Randomize leaf count per directory randomize the name of each leaf
            $randFilesCount = rand(1, $maxFolderFiles);
            for ($j = 0; $j < $randFilesCount; $j++) {
                $isPathComplete = count($generatedPaths) >= $pathsReturned;
                $isBranchFull = ($arrayCountChecker[$folderPath] >= $maxFolderFiles);
                $hasMaxedDirectoryLeaf = $arrayCountChecker['counter'] >= ($maxDepth * $maxFolderFiles);
                if ($isPathComplete || $hasMaxedDirectoryLeaf || $isBranchFull) {
                    break;
                }
                $arrayCountChecker[$folderPath] += 1;
                $arrayCountChecker['counter'] += 1;
                $newFile = $basePath . $folderPath . "/" . self::RandomString() . ".txt";
                $generatedPaths[] = $newFile;
            }
        }

        return $generatedPaths;
    }

    private static function RandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

//Default paths given as example
$paths = [
    '/home/user/folder1/folder2/kdh4kdk8.txt',
    '/home/user/folder1/folder2/565shdhh.txt',
    '/home/user/folder1/folder2/folder3/nhskkuu4.txt',
    '/home/user/folder1/iiskjksd.txt',
    '/home/user/folder1/folder2/folder3/owjekksu.txt',
];
PathManager::setPaths($paths);

//Convert each path into an array
$pathsToArray = PathManager::pathsToArray();
print_r($pathsToArray);

//Convert array to tree
$arrayTree = PathManager::pathsArrayToTree();
print_r($arrayTree);


//Recursively prints the array tree
PathManager::printTreeArray(5, 1);

//Random file path generator
$basePath = '/home/user';
$pathsReturned = 5;
$maxDepth = 3;
$maxFolderFiles = 1;

$randomFiles = PathManager::randomFileGenerator($basePath, $pathsReturned, $maxDepth, $maxFolderFiles);
print_r($randomFiles);
