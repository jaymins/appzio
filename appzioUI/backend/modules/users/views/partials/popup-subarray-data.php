<?php 
    $values_arr_entry = array_values( $data );
?>

<?php if ( !$count ) : ?>

    <tr>
        <th style="width: 10px">#</th>

        <?php foreach ($data as $arr_entry_heading => $value): ?>
            <th><?php echo strtoupper( $arr_entry_heading ); ?></th>
        <?php endforeach ?>

    </tr>

<?php endif; ?>

<tr>
    <td><?php echo $count+1 ?>.</td>

    <?php foreach ($values_arr_entry as $k => $inner_array_value): ?>
        <td><?php echo $inner_array_value; ?></td>
    <?php endforeach ?>
</tr>