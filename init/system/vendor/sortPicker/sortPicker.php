<?php
namespace SortPicker;

/**
 * Sort Class that contains difference sort method
 */

class SortPicker {
    /**
     * Insertion sort
     * more info: https://www.w3resource.com/php-exercises/searching-and-sorting-algorithm/searching-and-sorting-algorithm-exercise-3.php
     * @param Array $my_array [ 0, 2, 3 ,4 ,6 ,7]
     * Use Case: O(N) , O(NN) , O(NN)
     */
    public function insertion_sort($my_array) {
        ## loop over a first range
        for($i=0;$i<count($my_array);$i++){
            ## save current picker
            $val = $my_array[$i];
            
            ## perform insertion
            $j = $i-1;
            while($j>=0 && $my_array[$j] > $val){
                $my_array[$j+1] = $my_array[$j];
                $j--;
            }

            $my_array[$j+1] = $val;

        }
        
        return $my_array;
    }

    /**
     * Shell Short function 
     * More info: https://www.w3resource.com/php-exercises/searching-and-sorting-algorithm/searching-and-sorting-algorithm-exercise-5.php
     * @param Array $my_array [0,9,2, 3,4,6]
     * Use case: N(Log(N))
     */

    function shell_Sort($my_array) {
        
        ## calculate gap
        $gap = round(count($my_array)/2);

        while($gap > 0) {
            ## perform insertion based on gap
            for($i = $gap; $i < count($my_array);$i++) {

                ## $temp represents value to be inserted
                #  $j : position for insertion

                $temp = $my_array[$i];
                $j = $i;

                while($j >= $gap && $my_array[$j-$gap] > $temp)
                {
                    $my_array[$j] = $my_array[$j - $gap];
                    $j -= $gap;
                }

                $my_array[$j] = $temp;
            }
            ## reduce gap until 0
            $gap = round($gap/2);
        }
        
        return $my_array;
    }

    /**
     * Merge Sort in Ascendence
     * More information: https://www.startutorial.com/articles/view/data-structure-and-algorithm-merge-sort
     * 
     * @param Array [0, 2, 5, 2 , 11, 8, 7]
     */
    public function merge_sort($my_array) {
        /** Return if input contains only one value */
        if (count($my_array) <= 1) {
            return $my_array;
        }

        /**
         * 
         * Init Span of Merge Sort equal count(input) / 2
         */
        $array_left  = array_slice($my_array, 0, count($my_array) / 2);
        $array_right = array_slice($my_array, count($my_array) / 2);

        /** Recursive loop */
        $array_left = \SortPicker\SortPicker::merge_sort($array_left);
        $array_right = \SortPicker\SortPicker::merge_sort($array_right);

        /** sort two targetted arrays */
        return (function($array_left, $array_right) {
            /** init data */
            $data  = [];

            /** Compare and save two sorted arrays into temp array */
            while (count($array_left) > 0 && count($array_right) > 0) {
                if ($array_left[0] < $array_right[0] ) {
                    $data[] = array_shift($array_left);
                } else {
                    $data[] = array_shift($array_right);
                }
            } 

            /** if Array Left is abundant, add to temp array */
            for ($i = 0; $i < count($array_left); $i++) {
                $data[] = $array_left[$i];
            } 

            /** if Array Right is abundant, assign to temp */
            for ($i = 0; $i < count ($array_right); $i++) {
                $data[] = $array_right[$i];
            }

            /** retutn temp data
             * 
             */
            return $data;
        })($array_left, $array_right);
    }

    /**
     * Quick Sort
     * 
     * @var Array $array [0, 9, 3, 7, 8]
     * Use case: O(n) O(N*log (N)) O(N^2)
     */
    function quick_sort($array)
    {
        // find array size
        $length = count($array);
        
        // base case test, if array of length 0 then just return array to caller
        if($length <= 1){
            return $array;
        }
        else{
        
            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $array[0];
            
            // declare our two arrays to act as partitions
            $left = $right = array();
            
            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for($i = 1; $i < count($array); $i++)
            {
                if($array[$i] < $pivot){
                    $left[] = $array[$i];
                }
                else{
                    $right[] = $array[$i];
                }
            }
            
            // use recursion to now sort the left and right lists
            return array_merge(\SortPicker\SortPicker::quick_sort($left), array($pivot), \SortPicker\SortPicker::quick_sort($right));
        }
    }
}