<tr>
    <th style="width: 10px">#</th>
    <th>Entry</th>
</tr>

<?php
    $count = 1;
    foreach ($data as $i => $arr_value):
?>
    
    <tr>
        <td><?php echo $count++; ?>.</td>
        <td>
            <?php
                if ( is_array($arr_value) ) {
	                echo '<pre>';
	                print_r( $arr_value );
	                echo '</pre>';
                } else {
	                echo $arr_value;
                }
            ?>
        </td>
    </tr>                       
    
<?php endforeach ?>