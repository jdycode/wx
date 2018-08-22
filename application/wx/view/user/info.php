
<table>
    <tr>
        <th>用户</th>
        <th>电话</th>
        <th>余额</th>
        <th>积分</th>
    </tr>
    <?php foreach ($members as $member):?>
    <tr>
        <td>{$member.username}</td>
        <td>{$member.tel}</td>
        <td>{$member.money}</td>
        <td>{$member.jifen}</td>
    </tr>
    <?php endforeach;?>
</table>
