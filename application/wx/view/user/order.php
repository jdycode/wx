
    <table>
        <tr>
            <th>商家ID</th>
            <th>订单号</th>
            <th>详细地址</th>
            <th>总价</th>
        </tr>
        <?php foreach ($orders as $order):?>
            <tr>
                <td>{$order.shop_id}</td>
                <td>{$order.sn}</td>
                <td>{$order.detail_address}</td>
                <td>{$order.total}</td>
            </tr>
        <?php endforeach;?>
    </table>
