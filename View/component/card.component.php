<div class="card">
    <div class="player_info">
        <img src="<?="../../images/Aatrox.png"?>" alt="<?= "image champion" ?>"/>
        <div class="details_player_info">
            <p><?= "LvL" ?></p>
            <p><?= "Name" ?></p>
        </div>

    </div>

</div>

<style>
    .card{
        border: 1px solid black;
        width: 14rem;
        height:23rem;
    }
    .player_info {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        border-bottom: 1px solid black;
    }
    .details_player_info {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .player_info img {
        width: 30%;
    }

    .player_info p {
        margin-bottom: 0;
    }
</style>