
<div class="title_comment"><h3>Tambah Komentar (<?PHP echo $komentar;?>)</h3></div>
<div id="respon" class="komentar-respon">
<span class="batal-reply"><a href="#batal" class="reply" id="0" title="Batal"><i class="icon-remove"></i> Batal</a></span>
<div class="reply-title"><h3>Balas Komentar</h3></div>
</div>
<?PHP include "../lib/warning.mgb";//warning?>
<form method="post"  id="commentform" action="<?PHP echo MY_PATH?>action-comment/">
<div class="row-fluid comment_form" id="#komentar">
<div class="col-lg-6 col-md-6 col-sm-6 comment_form">
<input class="col-lg-12 col-md-12 col-sm-12 icon-nama" name="nama_comment" value="<?PHP echo $nama_comment;?>" type="text" placeholder="Nama anda">
</div>
<div class="col-lg-6 col-md-6 col-sm-6 comment_form">
<input class="col-lg-12 col-md-12 col-sm-12 icon-mail" name="email_comment" type="email" value="<?PHP echo $email_comment;?>"  placeholder="E-mail anda">
</div>
<div class="col-lg-12 col-md-12 col-sm-12 comment_form">
<input class="col-lg-12 col-md-12 col-sm-12 icon-link" name="url_website_komen" type="text" value="<?PHP echo $url_website_komen;?>"  placeholder="Alamat web anda">
</div></div>

<div class="row-fluid"><div class="col-lg-12 col-md-12 col-sm-12" id="#respon">
<textarea name="comment" rows="6" class="textarea" placeholder="Pesan"></textarea></div></div>

<div class="row-fluid margin-top-10">
<div class="col-lg-12 col-md-12 col-sm-12 margin-top-10">
<label>Masukkan kode captcha <span class="highlight1">*</span> </label></div>
<div class="col-lg-3 col-md-3 col-sm-3 comment_form">
 <img class="span5" src="<?PHP echo MY_PATH?>captcha.php" style="height:30px!important; width:110px;"/>
 </div>
<div class="col-lg-6 col-md-6 col-sm-6 contact_form">
<input class="span3" name="captcha" id="captcha" type="text" placeholder="Captcha *">
  </div></div>
<input type='hidden' name='parent_id' id='parent_id' value='0'/>
<input name="link_article" type="hidden" value="<?PHP echo $link_article;?>"/>
<input name="id_article" type="hidden" id="id_article"  value="<?php echo $a_id;?>"/>
<div class="col-lg-4 col-md-4 col-sm-4 margin-bottom-29">
<button type="submit" name="action_comment" class="btn btn-info btn-lg btn-block kirim"><i class="icon-signin"></i> KIRIM</button>
</div></form>