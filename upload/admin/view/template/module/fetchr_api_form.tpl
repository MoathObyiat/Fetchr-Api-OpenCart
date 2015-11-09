<?= $header; ?><?= $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
    <?php if ($api_error) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $api_error; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="pull-right">
        <a href="<?= $push; ?>" data-toggle="tooltip" title="<?= $button_push; ?>" class="btn btn-warning"><i class="fa fa-truck"></i>
        </a>

        <button type="submit" form="form-user" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i>
        </button>

        <a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i>
        </a>

      </div>
      <h1><?= $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-cog fa-fw"></i> <?= $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form-user" class="form-horizontal">
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-accounttype"><?= $entry_accounttype; ?></label>
            <div class="col-sm-10">
             <?php // echo $accounttype; ?>
              <select name="accounttype" id="input-accounttype" class="form-control">
                <?php if ($accounttype) { ?>
                <option value="0"><?= $text_staging; ?></option>
                <option value="1" selected="selected"><?= $text_live; ?></option>
                <?php } else { ?>
                <option value="0" selected="selected"><?= $text_staging; ?></option>
                <option value="1"><?= $text_live; ?></option>
                <?php } ?>
              </select>
              
            </div>
          </div>
            
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-servicetype"><?= $entry_servicetype; ?></label>
            <div class="col-sm-10">
             <?php // echo $servicetype; ?>
              <select name="servicetype" id="input-servicetype" class="form-control">
                <?php if ($servicetype) { ?>
                <option value="0"><?= $text_delivery; ?></option>
                <option value="1" selected="selected"><?= $text_fulfildelivery; ?></option>
                <?php } else { ?>
                <option value="0" selected="selected"><?= $text_delivery; ?></option>
                <option value="1"><?= $text_fulfildelivery; ?></option>
                <?php } ?>
              </select>
              
            </div>
          </div>
            <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-username"><?= $entry_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="username" value="<?= empty($username) ? '' : $username ; ?>" placeholder="<?= $entry_username; ?>" id="input-username" class="form-control" />
              <?php if ($error_username) { ?>
              <div class="text-danger"><?= $error_username; ?></div>
              <?php } ?>
            </div>
          </div>
          
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-password"><?= $entry_password; ?></label>
            <div class="col-sm-10">
              <input type="password" name="password" value="<?= $password; ?>" placeholder="<?= $entry_password; ?>" id="input-password" class="form-control" autocomplete="off" />
              <?php if ($error_password) { ?>
              <div class="text-danger"><?= $error_password; ?></div>
              <?php  } ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $footer; ?> 