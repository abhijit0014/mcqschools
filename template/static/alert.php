<!-- Important Day -->
<?php if(!empty($today)) { ?>
<div class="mb-2">
    <div class="row m-3 p-2 pt-3 pb-4 bg-white rounded shadow-sm">
        <div class="col-12 col-md-4">
            <span class="h4 text-success"><?php echo $today[0]['title'] ?></span>
            <div class="small"><?php echo date("l, d F Y", strtotime($current_date)); ?></div>
        </div>
        <div class="col-12 col-md-8">
            <!--
            <blockquote class="blockquote">
                <p class="mb-0">The Ocean: Life and Livelihoods</p>
            </blockquote>
            -->
            <span class="small text-secondary"><?php echo $today[0]['descp'] ?></span>
        </div>
    </div>
</div>
<?php } ?>

<!-- upcoming live exam -->

<div class="mb-2">
    <div class="row m-3 p-2 pt-4 pb-4 bg-white rounded shadow-sm border ">
        <div class="col-10 col-md-7">
            <span class="h4">WBP Preliminary Mock Test - Live</span>
            <div class="small text-secondary">10 AM - Sunday, 27 June 2021</div>
            <div class="d-flex bd-highlight">
                <div class="flex-fill bd-highlight">
                    <span class="h6">100</span> <span class="text-secondary">Question</span>
                </div>
                <div class="flex-fill bd-highlight">
                    <span class="h6">60 </span> <span class="text-secondary">mins</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 text-end">
            <a href="/examcenter/live" class="d-grid text-decoration-none"><button class="btn border border-primary mt-3">Details</button></a>
        </div>
    </div>
</div>

