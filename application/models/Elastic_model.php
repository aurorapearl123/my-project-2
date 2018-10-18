<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;


class Elastic_model extends CI_Model
{
    var $client;
    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
    }

    public function elastic_search($q)
    {
        //$q = $this->input->post('search');
        $results = [];
        $data = [];
        //$q = 'Marjorie';
        $query = $this->client->search([
            'body' => [
                'query' => [
                    'match' => [
                        "customer" => [
                            "query" => $q
                        ]
                    ]
                ]
            ]
        ]);

        if($query['hits']['total'] >= 1 ) {
            $results = $query['hits']['hits'];
////            foreach($results as $result) {
////                $data[] = $result['_source'];
////            }
        }

        $data = array_unique($results);
        return $data;
        //echo json_encode($data);
        //die();
    }

    public function search($data)
    {
        $results = [];
        $query = $this->client->search($data);
        if($query['hits']['total'] >= 1 ) {
            $results = $query['hits']['hits'];
        }


        return $results;

    }

    public function saveToElasticSearch($data)
    {
        $this->client->index($data);
    }

    public function update($data)
    {
        return $this->client->update($data);
    }

    public function delete($index, $type, $id)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id
        ];

        $response = $this->client->delete($params);
        return $response;
    }
}