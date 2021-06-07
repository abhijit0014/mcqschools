<!-- Important Day -->
<?php if(!empty($today)) { ?>
<div class="mb-2">
    <div class="row m-3 p-2 pt-3 pb-4 bg-white rounded shadow-sm">
        <div class="col-12 col-md-4">
            <span class="h4 text-success"><?php echo $today[0]['title'] ?></span>
            <div class="small"><?php echo date("l, d F Y", strtotime($current_date)); ?></div>
        </div>
        <div class="col-12 col-md-8">
            <span class="small text-secondary"><?php echo $today[0]['descp'] ?></span>
        </div>
    </div>
</div>
<?php } ?>

<!-- upcoming live exam -->
<!-- 
<div class="mb-2">
    <div class="row m-3 p-2 pt-4 pb-4 bg-white rounded shadow-sm">
        <div class="col-2 col-md-2">
            <button class="btn btn-success" type="button">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                LIVE
            </button>
     
            <span style=" vertical-align: 0.125em;" class="ps-2 pe-2">
                <img src="/template/icon/topic.svg" alt="topic" width="50px">
            </span>
   
        </div>
        <div class="col-7 col-md-4">
            <span class="h4">WBP Mock Test</span>
            <div class="small text-secondary">8 AM - Sunday, 12 June 2021</div>
            <div class="small text-secondary">100 Question . 60 mins</div>
        </div>
        <div class="col-3 col-md-4 text-end">
            <button class="btn border mt-2">Details</button>
        </div>
    </div>
</div>

-->