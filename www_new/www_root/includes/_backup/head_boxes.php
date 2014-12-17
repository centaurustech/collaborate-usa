<link rel="stylesheet" href="<?=DOC_ROOT?>assets/css/post_login.css" type="text/css" />

<div class="head_boxes">

<div class="searchhead">
    <div class="seartxt">Search</div>
    <div class="searinpt"><input name="" type="text" /></div>
    <div class="seargo"><a href="#">Go</a></div>
</div>

<?php /*<div class="usericons"></div>*/ ?>

<div id="nav">
<ul>
    <!-- Messages -->
    <li>
        <div class="iconsusrnav">
            <a href="<?=DOC_ROOT?>ecosystem/messages">
            <img src="<?=DOC_ROOT?>ecosystem/assets/images/message.jpg" /></a>
            <span class="posabt">10</span>
        </div>

        <?php #/* ?>
        <ul class="marginulin marginuliniph pad_1 nav_1">
            <li class="title width_390"><h2>Messages ::</h2></li>

            <li class="brdrnonebot width_390">
                <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
                <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat</a></strong></div>
                <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>

            <li class="brdrnonebot width_390">
            <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>

            <li class="width_390" style="background: #f4f6f8; padding:0px 0px 0 10px;">
                <a href="<?=DOC_ROOT?>ecosystem/messages" class="active">View More ...</a>
            </li>
        </ul>
        <?php #*/ ?>
    </li>


    <!-- Notifications -->
    <li>
        <div class="iconsusrnav">
            <a href="<?=DOC_ROOT?>ecosystem/notification">
                <img src="<?=DOC_ROOT?>ecosystem/assets/images/ghanta.jpg" />
            </a>
            <span class="posabt" style="margin-left:-10px;">10</span>
        </div>

        <?php #/* ?>
        <ul class="marginulin marginuliniph pad_1 nav_2">
            <li class="title width_390"><h2>Notifications ::</h2></li>

            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>

            <li class="width_390" style="background: #f4f6f8; padding:0px 0px 0 10px;">
                <a href="<?=DOC_ROOT?>ecosystem/notification" class="active">View More ...</a>
            </li>
        </ul>
        <?php #*/ ?>
    </li>


    <!-- Accounts -->
    <li>
        <div class="iconsusrnav">
            <a href="#">
            <img src="<?=@$prf_pic_th?>" style="width:35px; height:35px;" class="round_borders" /></a>
        </div>

        <ul class="marginuliniph pad_2 width_300 nav_3">
            <?php #/* ?><li class="title"><h2>My Account ::</h2></li>
            <li class="brdrnonebot"><a href="#_">My Profile</a></li>
            <li class="brdrnonebot"><a href="#_">Update Password</a></li><?php #*/ ?>
            <li><a href="<?=DOC_ROOT?>ecosystem/logout">Sign Out</a></li>
        </ul>
    </li>
</ul>
</div>
</div>