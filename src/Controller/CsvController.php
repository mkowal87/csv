<?php

namespace App\Controller;
/**
 * Created by PhpStorm.
 * User: mkowal
 * Date: 13.06.2019
 * Time: 18:24
 */
/**
 * Class CsvController
 *
 * @package App\Controller
 */
class CsvController
{

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * CsvController constructor.
     */
    public function __construct()
    {
        $config = include($_SERVER['DOCUMENT_ROOT'] . '/config.php');

        try {
            $this->connection = new \mysqli($config['host'], $config['user'], $config['password'], $config['name']);
        } catch (\Exception $e) {
            echo $e->getMessage() . '<br>' . 'There have been a connection issue';
        }
    }

    /**
     * Method to insert data of user
     *
     * @return bool
     */
    public function csvUpload(): bool
    {
        $filename = explode(".", $_FILES['file']['name']);
        $csvRow = fopen($_FILES['file']['tmp_name'], "r");
        $sql = 'INSERT 
                INTO csv (original_id, first_name, last_name, email, gender, country) 
                VALUES (?, ?, ?, ?, ?, ?);';

        while ($data = fgetcsv($csvRow)) {
            if ($data[1] == 'first_name' ||
                $data[2] == 'last_name' ||
                $data[3] == 'email' ||
                $data[4] == 'gender' ||
                $data[5] == 'ip_address'
            ) {
                continue;
            }
            $statement = $this->connection->prepare($sql);
            $statement->bind_param('ssssss',
                $data[0],
                $data[1],
                $data[2],
                $data[3],
                $data[4],
                $data[5]
            );
            $query = $statement->execute();
        }
        fclose($csvRow);
        return true;
    }

    /**
     * Method to obtain first 20 (only for visibility issues)
     * records grouped by numbers of country occupancies
     * @return string
     */
    public function csvGetRecordsByCountry() : string
    {
        $sql = 'SELECT COUNT(id) as number, country 
                FROM `csv` 
                GROUP BY country  
                ORDER BY number  DESC
                LIMIT 20';

        $query = $this->connection->query($sql);

        $data = [];
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
        }

        return json_encode($data);
    }
}