<?php


class ApiHelpers extends CI_Controller
{
    static function getCategoriesById($id)
    {
        self::get_instance()->db->select('clothes_categories.*');
        //$this->db->select('clothes_categories.*');
        self::get_instance()->db->from('clothes_categories');
        //$this->db->join ( 'clothes_categories', $table . '.clothesCatID=clothes_categories.clothesCatID', 'left' );
        self::get_instance()->db->where('clothes_categories.clothesCatID', $id);
        $details = self::get_instance()->db->get()->result();
        return $details;
    }

    static function getCategories($id)
    {
        self::get_instance()->db->select('*');
        self::get_instance()->db->where('status', 1);
        self::get_instance()->db->where('serviceID', $id);
        return self::get_instance()->db->get('clothes_categories')->result();
    }
}