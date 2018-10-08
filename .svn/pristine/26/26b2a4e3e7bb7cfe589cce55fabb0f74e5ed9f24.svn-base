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
//        self::get_instance()->db->select('*');
//        self::get_instance()->db->where('status', 1);
//        self::get_instance()->db->where('serviceID', $id);

        self::get_instance()->db->select('DISTINCT(category), price, serviceID, clothesCatID, status');
        self::get_instance()->db->group_by('category');
        self::get_instance()->db->where('serviceID', $id);
        return self::get_instance()->db->get('clothes_categories')->result();

        //$this->db->select('DISTINCT(category), price, serviceID, clothesCatID, status');
        //$this->db->group_by('category');
    }

//    static function getOrders($cusID, $branchID)
//    {
//
//        self::get_instance()->db->select('order_headers.*');
//        self::get_instance()->db->select('service_types.*');
//        self::get_instance()->db->select('customers.*');
//        self::get_instance()->db->select('branches.branchName');
//        self::get_instance()->db->from('order_headers');
//        self::get_instance()->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
//        self::get_instance()->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
//        self::get_instance()->db->join('customers', 'order_headers.custID=customers.custID', 'left');
//        self::get_instance()->db->where('order_headers.status',4);
//
//        self::get_instance()->db->where('DATE(order_headers.dateReady) >= ', Date('Y-m-d'));
//        self::get_instance()->db->where('DATE(order_headers.dateReady) <= ', Date('Y-m-d'));
//        self::get_instance()->db->where('order_headers.branchID', $branchID);
//        $orders = self::get_instance()->db->get()->result();
//        $data = [];
//        foreach($orders as $order) {
//            $data[] = [
//                'order_id' => $order->orderID,
//                'custID' => $order->custID,
//                'suffix' => $order->suffix,
//                'fname' => $order->fname,
//                'mname' => $order->mname,
//                'lname' => $order->lname,
//                'date' => $order->date,
//                'service_id' => $order->serviceID,
//                'service_type' => $order->serviceType,
//                'branch_id' => $order->branchID,
//                'branch_name' => $order->branchName,
//                'order_details' => self::get_instance()->getDetails('order_details',$order->orderID),
//            ];
//        }
//
//        return $data;
//    }

    public function getDetails($table, $orderID)
    {
        //join item
        $this->db->select('order_details.*');
        $this->db->select('service_types.*');
        $this->db->from('order_details');
        $this->db->join ('service_types', $table . '.serviceID=service_types.serviceID', 'left' );
        $this->db->where('order_details.orderID', $orderID);
        $details = $this->db->get()->result();

        $data = [];
        foreach($details as $detail) {
            $data[] = [
                'id' => $detail->id,
                'unit' => $detail->unit,
                'orderID' => $detail->orderID,
                'serviceID' => $detail->serviceID,
                'qty' => $detail->qty,
                'rate' => $detail->rate,
                'amount' => $detail->amount,
                'serviceType' => $detail->serviceType,
                'regRate' => $detail->regRate,
                'discountedRate' => $detail->discountedRate,
                'desc' => $detail->desc,
                'status' => $detail->status,
                'categories' => $this->order_detail_categories($detail->id)
            ];
        }
        return $data;
    }

    public function order_detail_categories($detailID)
    {
        $this->db->select('order_detail_categories.*');
        $this->db->select('clothes_categories.*');
        $this->db->from('order_detail_categories');
        $this->db->where('order_detail_categories.order_detail_id', $detailID);
        $this->join ('clothes_categories', 'order_detail_categories' . '.	clothes_catID=clothes_categories.	clothesCatID', 'left' );
        $details = $this->db->get()->result();
        return $details;
    }

    public function getOrdersDetailsById($orderID)
    {
        $this->db->select('order_headers.status as order_status');
        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        //$this->db->where('order_headers.status',4);
        $this->db->where('order_headers.orderID', $orderID);
        $orders = $this->db->get()->result();
        $data = [];
        foreach($orders as $order) {
            $data[] = [
                'order_id' => $order->orderID,
                'custID' => $order->custID,
                'suffix' => $order->suffix,
                'fname' => $order->fname,
                'mname' => $order->mname,
                'lname' => $order->lname,
                'rate' => $order->rate,
                'deliveryFee' => $order->deliveryFee,
                'amount' => $order->amount,
                'ttlAmount' => $order->ttlAmount,
                'discounted' => $order->isDiscounted,
                'date' => $order->date,
                'service_id' => $order->serviceID,
                'service_type' => $order->serviceType,
                'branch_id' => $order->branchID,
                'branch_name' => $order->branchName,
                'custsign' => $order->custSign,
                'status' => $order->order_status,
                'remarks' => $order->remarks,
                'order_details' => $this->getDetails('order_details',$order->orderID),
                //'order_categories' => $this->getOrderCategories('order_categories',$order->orderID)
            ];
        }

        return $data;
    }
}