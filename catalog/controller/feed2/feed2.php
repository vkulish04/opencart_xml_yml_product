<?php
class ControllerFeed2Feed2 extends Controller {

    public function index(){
        if (isset($this->request->get['id'])) {
            $id_store = $this->request->get['id'];
            $id_store = $id_store * 1500;
        } else {
            $id_store = 0;
        }
        $product = $this->product($id_store);
        $category = $this->category();
        $output  = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<yml_catalog date="' . date("Y-m-d H:i") .'">';
        $output .= '<shop>';
        $output .= '<name>Интернет-магазин одежды и обуви, заказы по каталогам МодноВсё</name>';
        $output .= '<url>' . $_SERVER["HTTP_HOST"] . '</url>';
        $output .= '<email>info@modnovse.ru</email>';
        $output .= '<currencies>
            <currency id="RUR" rate="1"/>
        </currencies>';
        $output .= '<categories>';
        $output .= $category;
        $output .= '</categories>';
        $output .= '<offers>';
        $output .= $product;
        $output .= '</offers>';
        $output .= '</shop>';
        $output .= "</yml_catalog>";
        $this->response->addHeader('Content-Type: application/xml');
        $this->response->setOutput($output);
    }
    public function product($id_store){
        $this->load->model('feed2/feed2');
        $this->load->model('catalog/product');
        $output = "";
        $res_prod = $this->model_feed2_feed2->getProduct($id_store);
        foreach($res_prod as $res){
            $name = str_replace("&", "s", $res['name']);
            $options = $this->model_catalog_product->getProductOptions($res['id']);
            $product_url = $this->url->link('product/product', 'product_id=' . $res['id']);
            $description = "<![CDATA[" . $res['description'] . "]]>";
            $image = "https://www.modnovse.ru/image/" . $res['src_image'];
            if(!@fopen($image,'r'))
            {
                $image = "https://www.modnovse.ru/image/logo-mv.jpg";
            }
            $output .= "<offer id='" . $res['id'] . "'>";
            $output .= "<name>" . $name . "</name>";
            if($res['manufacturer']){
                $output .= "<vendor>" . $res['manufacturer'] . "</vendor>";
            }
            $output .= "<url>" . $product_url . "</url>";
            $output .= "<price>" . $res['price'] . "</price>";
            $output .= " <currencyId>RUR</currencyId>";
            $output .= "<categoryId>" . $res['catetegory_id'] . "</categoryId>";
                $output .= "<picture>" . $image . "</picture>";
            $output .= "<description>" . $description . "</description>";
            $output .= "<store>true</store>";
            foreach($options as $option){
                if($option['product_option_value'][0]['name'] && $option['name']){
                    $output .= "<param name='" . $option['name'] . "'>" . $option['product_option_value'][0]['name'] . "</param>";
                }
            }
            $output .= "</offer>";
        }
        return $output;
    }
    public function category()
    {
        $this->load->model('feed2/feed2');
        $categories = $this->model_feed2_feed2->getCategory();
        $output = '';
        foreach($categories as $category){
            if($category['parent_id'] == 0)
            {
                $output .= "<category id='" . $category['id'] .  "'>" . $category['name'] . '</category>';
            }
            else{
                $output .= "<category id='" . $category["id"] .  "' parentId='" . $category['parent_id'] . "'>" . $category['name'] . '</category>';
            }

        }
        return $output;
    }
}