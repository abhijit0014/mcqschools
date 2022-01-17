<!-- WBP Result -->
<div class="mb-3 d-none">
        <div class="d-flex alert alert-warning shadow-sm border border-warning ">  
            <div class="flex-fill">
                <div class="h5">WBP 2021 - Solved Paper</div>    
            </div>
            <div class="">
                <a href="/downloads/WBP-ANSWER-KEY-2021.pdf" class="d-grid text-decoration-none">
                    <button class="btn btn-sm btn-primary">Download</button>
                </a>
            </div>
        </div>
</div>


<!-- live quiz -->
<?php if( $liveQuiz) {
    if(date_create($liveQuiz['start_time']) < date_create() && date_create($liveQuiz['end_time']) > date_create()) { 
?>
    <div class="mb-3">
        <div class="d-flex alert alert-warning shadow-sm border border-warning ">
            <div class="me-3">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            </div>    
            <div class="flex-fill">
                <span class="small"><span class="small text-secondary d-block">LIVE Quiz</span></span>
                <div class="h6"><?php echo $liveQuiz['title'] ?></div>    
            </div>
            <div class="">
                <a href="/quiz/live" class="d-grid text-decoration-none">
                    <button class="btn btn-sm btn-primary">Play Now</button>
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
<div class="mb-2 container">
    <div class="row p-2 pt-3 pb-3 bg-white border rounded shadow-sm">
        <div class="col-12 col-md-4">
            <span class="h5 text-success"><?php echo $today[0]['title'] ?></span>
            <div class="small mt-2 mb-1"><?php echo date("l, d F Y", strtotime($current_date)); ?></div>
        </div>
        <div class="col-12 col-md-8">
            <!--
            <blockquote class="blockquote">
                <p class="mb-0">The Ocean: Life and Livelihoods</p>
            </blockquote>
            -->
            <span class="small text-secondary lh-sm"><?php echo $today[0]['descp'] ?></span>
        </div>
    </div>
</div>
<?php } ?>

<!-- upcoming live exam -->
<?php if( $property->live_exam_id ) { ?>
<div class="mb-3">
        <div class="d-flex alert alert-info border border-info ">  
            <div class="flex-fill">
                <span class="badge bg-danger <?php echo date_create($liveExam['start_time']) < date_create() && date_create($liveExam['end_time']) > date_create()? ' ': 'd-none' ?>">Live</span>
                <div class="h5"><?php echo $liveExam['title'] ?></div> 
                <div class="small mb-0"><?php echo date_format(date_create($liveExam['start_time']),"l, dS F Y"); ?> </div>

                <div class="small">
                    <span class="me-2 text-nowrap">
                            <span class="fw-bold"> Starts on: </span>
                            <?php echo date_format(date_create($liveExam['start_time']),"h:i A"); ?>
                    </span>
                    <span class="text-nowrap">
                            <span class="fw-bold"> Ends on: </span>
                            <?php echo date_format(date_create($liveExam['end_time']),"h:i A"); ?>
                    </span> 
                </div>

                <a href="/examcenter/live" class="text-decoration-none d-block mt-3">
                    <div class="d-grid d-block d-md-inline">
                        <button class="btn ps-3 pe-3 fw-bold <?php echo date_create($liveExam['end_time']) > date_create()? 'btn-primary': 'btn-success' ?>">
                            <?php echo date_create($liveExam['end_time']) > date_create()? 'Attempt exam': 'Check exam result' ?>
                        </button>
                    </div>
                </a>
            </div>
            <div class="text-center d-none d-md-block flex-fill pt-2">
                <img src="/template/icon/online-test.png" alt="online exam" width="100px">
            </div>
        </div>
    </div>
<?php } ?>

