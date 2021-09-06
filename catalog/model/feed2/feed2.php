<?php

class ModelFeed2Feed2 extends Model {
    public function getProduct($id_store){
        $sql = "select distinct pd.name AS name, pd.description AS description, p.product_id AS id, p.image AS src_image, p.price AS price, (SELECT md.name FROM oc_manufacturer_description md WHERE md.manufacturer_id = p.manufacturer_id AND md.language_id = '1') AS manufacturer,(SELECT ss.name FROM oc_stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '1') AS stock_status,(SELECT category_id FROM oc_product_to_category ptc WHERE ptc.product_id = p.product_id LIMIT 1) AS catetegory_id from oc_product p LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id) WHERE pd.name is not null AND status = '1' limit " . $id_store .", 1500";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getCategory(){
        $sql="SELECT category_id AS id, parent_id, (SELECT cd.name FROM oc_category_description cd WHERE c.category_id = cd.category_id) AS name FROM oc_category c WHERE status = '1' ORDER BY parent_id ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }
}