<?php if (isset($table[0])): ?>
<table class="dbtable table-striped table-hover">
    <thead>
        <tr>
            <?php foreach ($table[0] as $col => $names): ?>
            <th><?= $col ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($table as $row): ?>
        <tr>
            <?php foreach ($row as $data): ?>
            <td><?= $data ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
No rows in this table
<?php endif; ?>