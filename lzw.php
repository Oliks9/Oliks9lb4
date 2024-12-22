<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LZW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
</style>
<body>
    <header>
        <div class="container">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/Oliks9lb4/lzw.php">LZW эффективное</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Oliks9lb4/lzw2.php">LZW циферки</a>
                </li>
            </ul>
        </div>
    </header>
    <div class="container">
    <h1>Алгоритм Лемпеля-Зива-Велча (LZW)</h1>
    <h2>Сжать</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToCompress" class="btn btn-outline-info" required>
        <div class="mt-2">
        <button type="submit" class="btn btn-outline-info btn-lg">Сжать</button>
        </div>
    </form>

    <h2>Распаковать</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToDecompress" class="btn btn-outline-warning" required>
        <div class="mt-2">
        <button type="submit" class="btn btn-outline-warning btn-lg">Распаковать</button>
        </div>
    </form>
    </div>
</body>
</html>

<?php

function lzw_encode($input) {

    $dict = [];
    $data = str_split($input);
    $out = [];
    $queue = $data[0];
    $code = 256;

    foreach (range(0, 255) as $i) {
        $dict[chr($i)] = $i;
    }

    for ($i = 1, $len = count($data); $i < $len; $i++) {

        $currentСharacter = $data[$i];

        if (isset($dict[$queue . $currentСharacter])) {

            $queue .= $currentСharacter;

        } 
        else {

            $out[] = $dict[$queue];
            $dict[$queue . $currentСharacter] = $code++;
            $queue = $currentСharacter;

        }
    }

    $out[] = $dict[$queue];
    return $out;
}

function lzw_decode($input) {

    $dict = [];

    foreach (range(0, 255) as $i) {
        $dict[$i] = chr($i);
    }

    $code = 256;
    $queue = chr($input[0]);
    $out = $queue;

    for ($i = 1, $len = count($input); $i < $len; $i++) {

        $currCode = $input[$i];

        if (isset($dict[$currCode])) {
            $entry = $dict[$currCode];
        } else {
            $entry = $queue . $queue[0];
        }

        $out .= $entry;
        $dict[$code++] = $queue . $entry[0];
        $queue = $entry;
    }

    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fileToCompress'])) {

        $fileContent = file_get_contents($_FILES['fileToCompress']['tmp_name']);
        $compressed = lzw_encode($fileContent);
        $binaryData = pack('n*', ...$compressed);


        // header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="compressed.txt"');
        echo $binaryData;
        exit;
    }

    if (isset($_FILES['fileToDecompress'])) {


        $binaryData = file_get_contents($_FILES['fileToDecompress']['tmp_name']);
        $codes = unpack('n*', $binaryData);
        $codes = array_values($codes);
        $decompressed = lzw_decode($codes);

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="decompressed.txt"');
        echo $decompressed;
        exit;
    }
}

?>

