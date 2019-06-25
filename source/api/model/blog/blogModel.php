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

    /**
     * Delete a row in table `blog`
     * 
     * @param string $id 
     */
    public function deleteARecord($id) {
        if (is_string($id)) {
            
            $sql = "DELETE FROM `" . DB_PREFIX . "blog` WHERE `id` = :id LIMIT 1";
            $query = $this->db->query($sql, [
                ':id'       => $id
            ]);

            return [
                'success'   => true,
                'data'      => $query->rowsCount()
            ];
        }

        return [
            'success'   => false,
            'message'   => 'Parameter id is not string type'
        ];
    }

    /**
     * Get a row in table blog
     * 
     * @param string $id
     */
    public function getARecord($id) {
        if (is_String($id)) {
            
            $sql = "SELECT * FROM `" . DB_PREFIX . "blog` WHERE `id` = :id LIMIT 1";
            $query = $this->db->query($sql, [
                ':id'   => $id
            ]);

            return  [
                'success'   => true,
                'data'      => $query->rows()
            ];

        }

        return [
            'success'   => false,
            'message'   => 'Parameter id is not string type'
        ];
    }

    /**
     * List data from table `blog`
     * 
     * @param number limit
     * @param number offset
     * @param string tags
     * @param string category
     * @param timestamp timestamp
     */
    public function listRecords($data) {

        // split and filter tags
        if (isset($data['tags']) 
            && is_string($data['tags'])) {
            foreach(explode(",", $data['tags']) as  $key=>$tag) {
                if  (!empty($tag = trim($tag))) {
                    $tags[':tag' . $key] = $tag;
                }
            }
        } else {
            $tags = [];
        }

        // split and filter category
        if (isset($data['category']) 
            && is_string($data['category'])) {
            foreach(explode(",", $data['category']) as $key=>$category) {
                if(!empty($category = trim($category))) {
                    $categories[':category' . $key ] = $category;
                }
            }
        } else {
            $categories = [];
        }

        $timestamp = isset($data['timestamp']);

        if (is_numeric($data['offset']) 
            && is_numeric($data['limit'])) {

            $sql = "SELECT * FROM `" . DB_PREFIX . "blog`";
            $sql_count = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "blog`";

            $sql_regex_data = [];
            $sql_data = array_merge($tags, $categories);
            $sql .= (!empty($sql_data) ? " WHERE" : "");
            $sql_count .= (!empty($sql_data) ? " WHERE" : "");

            if (!empty($tags)) {
                
                $regexp = "^(.*" . implode("[,$]|.*", array_values($tags)) ."[,$])";
                $sql_regex_data ['tag'] = $regexp;
                $sql .= " `tags` REGEXP :tag";
                $sql_count .= " `tags` REGEXP :tag";
            }

            if (!empty($categories)) {
                $sql .= (!empty($tags) ? " AND" : "");
                $sql_count .= (!empty($tags) ? " AND" : "");
                $regexp = "^(.*" . implode("[,$]|.*", array_values($categories)) . "[,$])";
                $sql_regex_data['categories'] = $regexp;
                $sql .= " `category` REGEXP :categories";
                $sql_count .= " `category` REGEXP :categories";
                
            }

            $sql .= ($timestamp ? " ORDER BY `timestamp`" : "");
            $sql .= " LIMIT " . $data['offset'] .", " . $data['limit'] . "";
            
            $query_data = $this->db->query($sql, $sql_regex_data)->rows();
            $query_data_count = $this->db->query($sql_count, $sql_regex_data)->row('total');

            return [
                'success'   => true,
                'data'      => $query_data,
                'total'     => $query_data_count,
                'limit'     => $data['limit'],
                'offset'    => $data['offset']
            ];
        }

        return [
            'success'   => false,
            'message'   => 'Parameter `offset` or `limit` is not number type'
        ];
    }
 }