<?php
    // Como usar - Basta digitar 'php backup.php' no terminal, o arquivo será baixado para a pasta backups
    
    // Database credentials
    $host = '195.179.239.102';
    $username = 'u234488260_meetmed';
    $database = 'u234488260_meetmed';
    $password = 'A!x8B3TeRR@BA$3@rEA';

    // Name of the backup file with current date
    $backupFile = 'backup/backup_' . date("Y-m-d_H-i-s") . '.sql';

    // Command to generate the SQL backup
    // $command = "mysqldump --user={$username} --password={$password} -h {$host} {$database} > {$backupFile}";
    $command = "mysqldump --user=" . escapeshellarg($username) .
    " --password=" . escapeshellarg($password) .
    " --host=" . escapeshellarg($host) .
    " " . escapeshellarg($database) .
    " > " . escapeshellarg($backupFile);
    // Execute the command
    system($command, $output);

    // Check if the backup was successful
    if ($output === 0) {
        echo "Backup successful! Saved as {$backupFile}\n";
    } else {
        echo "Backup failed!\n";
    }
?>
