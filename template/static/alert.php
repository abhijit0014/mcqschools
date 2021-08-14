<!-- live quiz -->
<?php if( $liveQuiz) {
    if(date_create($liveQuiz['start_time']) < date_create() && date_create($liveQuiz['end_time']) > date_create()) { 
?>
    <div class="mb-3 d-none">
        <div class="row m-1 p-2 pt-4 pb-4 alert alert-warning shadow-sm border border-warning ">
            <div class="col-10 col-md-7">
                <span class="badge bg-success">Live Quiz</span>
                <div class="h4">Independence Day Quiz 2021 <?php echo $liveQuiz['title'] ?></div>  
                <div class="small h6 text-secondary">Daily 7PM - 10PM</div>    
            </div>
            <div class="col-12 col-md-4 text-end">
                <a href="/quiz/live" class="d-grid text-decoration-none">
                    <button class="btn border border-secondary mt-3">Play Now</button>
                </a>
            </div>
        </div>
    </div>

<?php 
    }
} 
?>

<!-- Important Day -->
<?php if(!empty($today)) { ?>
<div class="mb-2">
    <div class="row m-1 p-2 pt-4 pb-4 bg-white border rounded shadow-sm">
        <div class="col-12 col-md-4">
            <span class="h4 text-success"><?php echo $today[0]['title'] ?></span>
            <div class="small mt-2 mb-1"><?php echo date("l, d F Y", strtotime($current_date)); ?></div>
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
<?php if( $GLOBALS["LIVE_EXAM_ID"]) { ?>
<div class="mb-3 container">
    <a href="/examcenter/live" class="text-decoration-none">
    <div class="row p-2 pt-3 pb-3 alert alert-info shadow-sm border border-info ">
        <div class="col-12">
            <span class="badge bg-success <?php echo date("Y-m-d H:i:s",  strtotime($liveExam['start_time'])) > date('Y-m-d H:i:s') && date("Y-m-d H:i:s",  strtotime($liveExam['end_time'])) < date('Y-m-d H:i:s') ? ' ': 'd-none' ?>">Live</span>
            <div class="h5"><?php echo $liveExam['title'] ?></div>
        </div>
        <div class="col-12">
            <div class="small h6 mb-0 text-secondary"><?php echo date_format(date_create($liveExam['start_time']),"l, dS F Y"); ?> </div>
            <div class="d-flex bd-highlight text-secondary">
                <div class="flex-fill bd-highlight">
                    <span class="small h6">STARTS ON: 
                        <span class="text-nowrap"><?php echo date_format(date_create($liveExam['start_time']),"h:i A"); ?></span> 
                    </span>
                </div>
                <div class="flex-fill bd-highlight">
                    <span class="small h6">ENDS ON: 
                        <span class="text-nowrap"> <?php echo date_format(date_create($liveExam['end_time']),"h:i A"); ?></span> 
                    </span>
                </div>
            </div>            
        </div>
    </div>
    </a>
</div>
<?php } ?>

