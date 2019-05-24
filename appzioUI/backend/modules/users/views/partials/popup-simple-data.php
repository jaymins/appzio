<?php if ( $count == 0 ) : ?>

	<tr>
	    <th style="width: 10px">#</th>
	    <th>Entry</th>
	</tr>

<?php endif; ?>

<tr>
    <td><?php echo $count + 1; ?>.</td>
	<td><?php echo $data; ?></td>
</tr>