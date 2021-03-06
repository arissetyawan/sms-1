@extends('app')
@section('content')
<style>
  .ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 200px;
  }
  </style>
<body onload="firstLoad(window.location.hash.substring(1));">
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">

			<div class="panel panel-default">
				<div class="panel-heading">Kotak Masuk 
					<div class="pull-right">
						<a class="btn-sm" title="Buat SMS baru" href="inbox"><span style="color:green" class="glyphicon glyphicon-envelope"></span>SMS baru</a>
						<?php $gr = (Session::get('group')=='Off') ? '1' : '0' ; ?>
							<!-- <a href="{{url('inbox/'.$gr.'/edit')}}" class="btn btn-default btn-sm <?php if(!$gr) echo 'active'; ?>" >Grouping : {{Session::get('group')}}</a> -->
					</div>
				</div>

				<div class="panel-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<input type="search" id="search" class="form-control input-sm" placeholder="Pencarian: masukkan nama atau nomor">
					      	<div class="input-group-btn">
						        <button type="button" id="filter" value="0" class="btn btn-default dropdown-toggle input-sm" data-toggle="dropdown" aria-expanded="false">Semua<span class="caret"></span></button>
						        <ul class="dropdown-menu" role="menu">
						          <li><a href="#">Semua</a></li>
						          <li class="divider"></li>
						        @foreach($data['list_keyword'] as $key)
						          <li><a href="#">{{$key->keyword}}</a></li>
						        @endforeach
						        </ul>
					      	</div><!-- /btn-group -->
					  	</div>
				      	<script type="text/javascript">
				      	$(".dropdown-menu li a").click(function(){
							$(this).parents(".input-group-btn").find('.btn').html($(this).text()+'<span class="caret"></span>');
							getData($('#search').val(),1,$(this).text());
						});
				      </script>

						<div style="height:500px;overflow-x:hidden;overflow-y:auto">
							<div class="list-group" id="listinbox">
								{{-- LIST OF CONTENT --}}
							</div>
							{{-- <div id="pagination" align="center">
							  <ul class="pagination pagination-sm">
							    <li><a id="prev" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
							    <li><a id="next" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
							  </ul>
							</div> --}}
						</div>
						{{-- <a class="btn btn-danger" href="#" onClick="Hapus()">Hapus yang dipilih?</a> --}}
						<select id="action" class="form-control input-sm">
							<option value="0" selected="selected">--Kelola pilihan--</option>
							<option value="1">Hapus</option>
							<option value="2">Export</option>
						</select>
					</div>

					<div class="col-md-8">
						<div class="panel panel-default">
							<div id="title" class="panel-heading"></div>
							<div name="detail" id="description" class="panel-body" style="height:458px;overflow-x:hidden;overflow-y:auto">
								<div id="detail"></div>

								<form id="form0" class="form-horizontal">
									  
									  <div class="form-group">
									    <label for="destination" class="col-sm-2 control-label">Tujuan</label>
									    <div class="col-sm-10" id="prefetch">
									      <input type="text" name="destination" class="form-control input-sm" id="destination" placeholder="Isi nomor, nama kontak atau nama group">
									      {{-- <select name="destination[]" class="chosen-select" tabindex="4" multiple=""></select> --}}
									    </div>
									  </div>
									  <div class="form-group">
									    <label for="text" class="col-sm-2 control-label">Isi SMS</label>
									    <div class="col-sm-10">
									      <textarea id="msg0" onkeyup="countText(event, this.value)" class="form-control input-sm" id="text" placeholder="Isi SMS" required>{{Input::get('text')}}</textarea>
									      <div class="help-block" id="textcount"></div>
									    </div>
									  </div>

									  <div id="schedule-button" class="form-group">
									    <label for="text" class="col-sm-2 control-label"></label>
									  	<div class="col-sm-10">
										  	<a class="btn btn-warning btn-xs" onclick="showScheduleForm()">Jadwalkan pengiriman</a>
									  	</div>
									  </div>
									  <input type="hidden" id="scheduled" value="0">

									  <script type="text/javascript">
									  	function showScheduleForm(){
									  		$('#schedule-button').hide();
									  		$('#schedule-form').show();
									  		$('#scheduled').val('1');
									  	}
									  	function hideScheduleForm(){
									  		$('#schedule-button').show();
									  		$('#schedule-form').hide();
									  		$('#scheduled').val('0');
									  	}
									  </script>
									  
									  <div id="schedule-form" class="form-group" style="display:none;">
									    <label for="send_date" class="col-sm-2 control-label">Waktu Kirim</label>
									    <div class="col-sm-3">
									      <input type="text" id="send_date" class="form-control input-sm" id="send_date" data-date-format="yyyy-mm-dd" placeholder="Tanggal Kirim"> 
									      <p class="help-block">tanggal</p> 
									    </div>

									  <script type="text/javascript">
									  	$(function () {
									  		$('#send_date').datepicker({
									  			dateFormat: "yy-mm-dd"
									  		});
									  	});
									  </script>

									    <div class="col-sm-2">
									      <select id="hour" class="form-control input-sm">
									      	@for ($i=0; $i < 24; $i++) 
									      		<option>{{sprintf("%02d",$i)}}</option>
									      	@endfor
									      </select>
									      <p class="help-bloc">jam</p>
									    </div>
									    <div class="col-sm-2">
									      <select id="minute" class="form-control input-sm">
									      	@for ($i=0; $i < 60; $i++) 
									      		<option>{{sprintf("%02d",$i)}}</option>
									      	@endfor
									      </select>
									      <p class="help-block">menit</p>
									    </div>
									    <div class="col-sm-2">
										  	<a class="btn btn-warning btn-xs" onclick="hideScheduleForm()">Batalkan</a>
									  	</div>
									  </div>									 

									  <div class="form-group">
									    <div class="col-sm-offset-2 col-sm-10">
									    @if(Session::has('access_token'))
									    	<btn class="btn btn-info btn-xs">Terhubung dengan {{'@'.Session::get('access_token')['screen_name']}} </btn><br>
									    	<input type="checkbox" name="twit" value="1"> Twit pesan ini?
									    @else
									    	<a class="btn btn-info btn-xs" href="{{url('twitter/connect')}}">Hubungkan dengan twitter?</a><br>
									    @endif
									    	<a id="submit-button" onclick="Send()" class="btn btn-default btn-sm">Kirim</a>
									    </div>
									  </div>
									</form>

							</div>
							<div id="form" class="input-group">
					          <textarea id="msg" onkeyup="countText(event, this.value)" name="msg" class="form-control" style="resize:none" rows="2" required></textarea>
					          
					          <a class="input-group-addon btn btn-primary" onclick="Send(window.location.hash.substring(1));"><span class="glyphicon glyphicon-send" ></span> Kirim <div class="help-block" id="textcount2"></div></a>
					        </div>
							
						</div>
					</div>
				</div>

				<div class="row">
				</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

/* ON FIRST LOAD */
	function firstLoad (detail) {
		getData();
		detail = (typeof detail !== 'undefined') ? detail : '';
		Detail(detail);
	}

	/* READY FUNCTION */
	$(document).ready(function(){
		/* SEARCH */
		$("#search").keyup(function(){
			filter = $('#filter').text();
			getData($(this).val(),1,filter);
		});
	});

	/* PAGINATION */
	var current_page = '';
	var last_page = '';
	$("#next").click(function(){
			// alert(last_page);
		if(current_page<last_page){
			getData('', current_page+1);
		}
	});
	$("#prev").click(function(){
		if(current_page>1){
			getData('', current_page-1);
		}
	});

	// action select
	$("#action").change(function(){
		if($(this).val() == '1'){
			Hapus();
			$("#action").val('0');
		}else if($(this).val() == '2'){
			Export();
			$("#action").val('0');
		}
	});

// count msg
function countText (e, val) {
	var res = 0;
	res = val.length;
	if(res >= 160){
		res = res+' / '+Math.ceil(res / 160);
	}
	// console.log(res);
	$('#textcount').html('Karakter: '+res);
	$('#textcount2').html(res);
}

//AUTOCOMPLETE destination number
	$(function() {
		function split( val ) {
	      return val.split( /,\s*/ );
	    }
	    function extractLast( term ) {
	      return split( term ).pop();
	    }
		$( "#destination" )
			.bind( "keydown", function( event ) {
	        if ( event.keyCode === $.ui.keyCode.TAB &&
	            $( this ).autocomplete( "instance" ).menu.active ) {
	          event.preventDefault();
	        }
	      	})

			.autocomplete(
			{
				minLength: 0,
				// source: "{{url('group')}}/0",
				source: function( request, response ) {
		          $.getJSON( "{{url('contact')}}/0", {
		            term: extractLast( request.term )
		          }, response );
		        },
				focus: function() {
		          return false;
		        },
				select: function( event, ui ) {
					var terms = split( this.value );
			          terms.pop();
			          terms.push( ui.item.label );
			          // console.log(ui.item.value);
			          terms.push( "" );
			          this.value = terms.join( ", " );
					$("#destination").val(this.value);
					return false;
				}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
		      return $( "<li>" )
		        .append( "<li class=\"list-group-item\" role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\">" + item.label + " (" + item.num + ")</a></li>" )
		        .appendTo( ul );
		    };
	});

	function checkAll(source) {
		checkboxes = document.getElementsByName('cid[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
		   checkboxes[i].checked = source.checked;
		}
		// document.getElementById("del").innerHTML = '<a href="#" onClick="Delete()">Delete data ini?</a>';
	}

	function loadListData (page,term,filter,callback) {
		$.get("{{url('inbox')}}", {page:page,term:term,filter:filter}, callback)
		.done(function (res) {
    		// if(filter && filter!=='Semua') localStorage.setItem("inboxList-"+filter, JSON.stringify(res));
		});
	}

	function showListData (data,status) {
		var res=nama=saveicon= '';
		// current_page = data['current_page'];
		// last_page = data['last_page'];
		$.each(data, function(i, item) {
			if(item.Name!==null){
				saveicon='';
				nama = item.Name;
			}else{
				saveicon='<a class="pull-right" title="Add to contact" href={{url("contact")}}#!/add/'+item.hp+'><span style="color:green" class="glyphicon glyphicon-floppy-disk"></span></a>';
				nama=item.hp;
			}
			if(item.status == 'false') nama = '<b>'+nama+'</b>';
		    res += '<div id="l-'+item.hp+'" onclick="Detail(\''+item.hp+'\');" class="list-group-item" style="cursor:pointer;"><p class="list-group-item-heading"><input name="cid[]" value="'+item.hp+'" type="checkbox" class="cg"> '+nama+' '+saveicon+'</p><p class="list-group-item-text">'+item.isi.substring(0,30)+'</p></div>';
		});
		$("#listinbox").html(res);
	}

	/* GET DATA FROM SERVER */
	function getData (term,page,filter) {
		term = typeof term !== 'undefined' ? term : '';
		page = typeof page !== 'undefined' ? page : 1;
		filter = typeof filter !== 'undefined' ? filter : '';
		$(document).bind("ajaxStart.mine", function() {
			$("#listinbox").html('<img src="{{asset("img/loadsmall.gif")}}">');
		});
		$(document).bind("ajaxStop.mine", function() {
			// alert('loaded');
		});
		
		if(localStorage.getItem("inboxList-"+filter)){
			dt = JSON.parse(localStorage.getItem("inboxList-"+filter));

			// console.log(dt);
			showListData(dt);
		}
		else{
			loadListData(page,term,filter,showListData);
		}
		
		$(document).unbind(".mine");
	}

	function Hapus(){
		var checkboxes = document.getElementsByName('cid[]');
		var vals = [];
		for (var i=0, n=checkboxes.length;i<n;i++) {
		  if (checkboxes[i].checked) 
		  {
		  vals[vals.length] = checkboxes[i].value;
		  }
		}
		if(vals.length){
			swal({
				title: "Anda yakin?",
				text: "Data yang sudah dihapus tidak dapat dikembalikan!",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Hapus!",   
				closeOnConfirm: false }, 

				function(){   
					$.post("{{url('inbox')}}/"+vals,
					{
						id:vals,
						_method:"DELETE",
						_token:"{{csrf_token()}}"
					},
					function(data,status){
						if(status=='success'){
					    	swal("Terhapus!", data+" data telah dihapus.", "success");
					    	firstLoad();
						}
					});
				});
		}else{
			swal({title: "Pilih data yang akan dihapus!",text: "Akan tertutup setelah 2 detik.",timer: 2000,type: "info" });
		}
	}

	function Export(){
		var checkboxes = document.getElementsByName('cid[]');
		var vals = [];
		for (var i=0, n=checkboxes.length;i<n;i++) {
		  if (checkboxes[i].checked) 
		  {
		  vals[vals.length] = checkboxes[i].value;
		  }
		}
		if(vals.length){
			swal({
				title: "Export data?",
				text: "Anda aka mengeksport data yang dipilih!",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Export!",   
				closeOnConfirm: true }, 

				function(){   
					location.href="{{url('inbox/export')}}/"+vals;
				});
		}else{
			swal({title: "Pilih data yang akan diexport!",text: "Akan tertutup setelah 2 detik.",timer: 2000,type: "info" });
		}
	}

	function deleteId(tabel,value) {
			swal({
				title: "Anda yakin?",
				text: "Data yang sudah dihapus tidak dapat dikembalikan!",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Delete!",   
				closeOnConfirm: true }, 

				function(){   
					$.post(tabel+"/"+value,
					{
						id:value,
						_method:"DELETE",
						_token:"{{csrf_token()}}"
					},
					function(data,status){
						if(status=='success'){
					    	// swal("Deleted!", data+" data has been deleted.", "success");
					    	Detail(window.location.hash.substring(1));
						}
					});
				});		
	}

	function Detail(phone)
		{
			if(phone){
				// phone = '0'+phone;
				$("[id^='l-']").removeClass('active');
				$("#l-"+phone).addClass('active');
			    $("#form0").hide();
			    $(document).ajaxStart(function(){
			        $("#title").html('<img src="{{asset("img/loadsmall.gif")}}">');
			    });
			    $(document).ajaxError(function(event, jqxhr, settings, exception) {
				    if (jqxhr.status==401) {
				        location.reload(false);
				    }
				});
				$.get("{{url('inbox')}}/"+phone, function(data,status){
					location.hash = phone;
				    var response = '';
				    var style = '';
				    var tag = '';
				    var btn0 = '';
				    var btn = '';
					for (var i = 0; i < data.length; i++) {
						btn0 = ' <a title="Forward this message" href="?text='+encodeURIComponent(data[i]['isi'])+'"><span class="glyphicon glyphicon-arrow-right"></span></a>';
						if(data[i]['tabel']=='inbox'){
							style = "alert-success pull-left";
							btn = btn0;
						}else if(data[i]['tabel']=='sent'){
							style = "alert-info pull-right";
							btn = btn0;
						}else{
							style = "alert-warning pull-right";
							btn = btn0+' <a title="Delete" onclick="deleteId(\'outbox\', '+data[i]['id']+')" href="#'+phone+'"><span style="color:red" class="glyphicon glyphicon-trash"></span></a>';
						}
						if (data[i]['udh']!='') {tag='div';}else{tag='div';}
						var auth = data[i]['author'].split('.');
						var author = link = '';
						// console.log(auth[0]);
						if(auth[0]=='apis'){
							author = data[i]['apis_name'];
							link = './api#'+auth[1];
						}else if(auth[0]=='keywords'){
							author = data[i]['keywords_name'];
							link = './keyword#'+auth[1];
						}else if(auth[0]=='users'){
							author = data[i]['users_name'];
							link = './user#'+auth[1];
						}else{
							author = data[i]['author'];
							link = './modem';
						}

						response +='<'+tag+' class="col-md-8 alert '+style+'">'+data[i]['isi']+'<br><small><a href="'+link+'">'+author+'</a> : <a href="#!/detail/'+data[i]['id']+'">'+data[i]['waktu']+'</a></small><div class="pull-right">'+btn+'</div></'+tag+'>';
					};
			    	$('#detail').html(response);
			    	var nama='';
			    	if (data[0]['Name']) { nama=data[0]['Name']+" - " };
			    	document.getElementById("title").innerHTML=nama+data[0]['hp'];
			    	var myDiv = document.getElementById("description");
					myDiv.scrollTop = myDiv.scrollHeight;
			    	// document.getElementById("msg").focus();
				    $("#form").show();

				});
			}else{
			    document.getElementById("title").innerHTML='Buat SMS baru';
			    $("#form0").show();
			    $("#form").hide();
			}
		}

	function formCompose(){
		$("input#destination").val('');
    	$("textarea#msg0").val('');
    	hideScheduleForm();
	}

	function Send (phone) 
	{
		var dest = state = msg = schedule = '';
		if(phone){ //conversation
			state = 1;
			dest = phone;
			msg = $("textarea#msg").val();
		}
		else //compose
		{
			state = 0;
			dest = $("input#destination").val();
			msg = $("textarea#msg0").val();
			if($('#scheduled').val()=='1'){
				schedule = $("input#send_date").val()+' '+$('#hour').val()+':'+$('#minute').val()+':00';
			}
		}
		swal({
				title: "Anda yakin?",
				text: "Akan mengirim SMS ke '"+dest+"'?",   
				type: "info",   
				showCancelButton: true,   
				// confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Kirim",   
				closeOnConfirm: true }, 

				function(){ 
					$.post("{{url('inbox')}}",
					{
						destination:dest,
						state:state,
						message:msg,
						schedule:schedule,
						_token:"{{csrf_token()}}"
					},
					function(data,status){
						if(status=='success'){
							if(state){
								$("textarea#msg").val('');
						    	Detail(window.location.hash.substring(1));
						    }else{
						    	firstLoad();
						    	formCompose();
						    }
						}
					});
				});
	}
</script>

@endsection