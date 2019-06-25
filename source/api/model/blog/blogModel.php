<?php
/**
 * Model for table `blog`
 */

 class BlogModel extends BaseModel {

    /**
     * Add New Record
     * 
     * @param string title - required - unique value 
     * @param string des - optional 
     * @param string tags - optional
     * @param string category - optional
     * @param string seo_title - required - unique value
     * @param string seo_des - optional
     * @param string seo_url - required - unique value
     */
    public function addNewRecord(Array $data) {
        $config = [
            'title'     => [ 'required' => true ],
            'des'       => [ 'required' => false, 'default' => ''],
            'tags'      => [ 'required' => false, 'default' => ''],
            'category'  => [ 'required' => false, 'default' => ''],
            'seo_title' => [ 'required' => true ],
            'seo_des'   => [ 'required' => false, 'default' => ''],
            'seo_url'   => [ 'required' => true,]
        ];

        try {
            array_walk($config, function($item, $key, $data) {
                if (isset($data[$key])) {
                    return;
                }
    
                if (!$item['required']) {
                    $data[$key] = (!$item['required']) ? $item['default'] : null;
                } else {
                    throw new \Exception($key . ' parameter does not exist');
                }
            }, $data);
        } catch (\Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage()
            ];
        }

        ## Count numbers of title
        $sql_count_title     = "SELECT COUNT(*) as total FROM `" . DB_PREFIX ."blog` WHERE `title` = :title LIMIT 1"; 
        if ($this->db->query($sql_count_title, [
            ':title'    => $data['title']
        ])->row('total')) {
            return [
                'success'   => false,
                'message'   => 'There is already an article named ' . $data['title']
            ];
        }

        ## Count number of seo title
        $sql_count_seo_title = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "blog` WHERE `seo_title` = :seo_title LIMIT 1";
        if ($this->db->query($sql_count_seo_title, [
            ':seo_title'    => $data['seo_title']
        ])->row('total')) {
            return [
                'success'   => false,
                'message'   => 'There is already an article has seo_title as ' . $data['seo_title']
            ];
        }

        ## Count number of seo url
        $sql_count_seo_url   = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "blog` WHERE `seo_url` = :seo_url LIMIT 1";
        if ($this->db->query($sql_count_seo_url, [
            ':seo_url'      => $data['seo_url']
        ])->row('total')) {
            return [
                'success'   => false,
                'message'   => 'There is already an article with seo_url as ' . $data['seo_url']
            ];
        }
       
        ## Insert a record into table blog
        $sql   = "INSERT INTO `" . DB_PREFIX . "blog` (`title`, `des`, `tags`, `category`, `seo_title`, `seo_des`, `seo_url`) VALUES (:title, :des, :tags, :category, :seo_title, :seo_des, :seo_url)";
        $query = $this->db->query($sql, [
            ':title'        => $data['title'],
            ':des'          => $data['des'],
            ':tags'         => $data['tags'],
            ':category'     => $data['category'],
            ':seo_title'    => $data['seo_title'],
            ':seo_des'      => $data['seo_des'],
            ':seo_url'      => $data['seo_url']
        ]);

        return [
            'success'       => true, 
            'data'          => $query->rowsCount()
        ];
    }
 }