<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Backups</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Back up actions</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
						<div class="btn-group m-b-10" role="group" aria-label="Backup actions">
							<a href="<?php echo base_url('Backup/files')?>" class="btn btn-default"><i class="os-icon os-icon-download-cloud"></i>Backup Files</a>
							<button id="startNodeBackup" class="btn btn-success"><i class="os-icon os-icon-database"></i>Start Backup (Node)</button>
						</div>

						<div id="progress" class="alert alert-info" style="display:none; margin-top:10px;"></div>

						<h5 class="m-t-15">Existing Backups</h5>
						<div class="table-responsive">
							<table class="table table-sm table-hover">
								<thead>
									<tr>
										<th>File</th>
										<th>Size</th>
										<th>Modified</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="backupList"></tbody>
							</table>
						</div>
		</div>
	</div>
</div>
<script>
(function(){
	const svc = 'http://localhost:5051';
	const listEl = document.getElementById('backupList');
	const progress = document.getElementById('progress');
	function fmtSize(bytes){
		if(bytes < 1024) return bytes + ' B';
		if(bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
		return (bytes/1024/1024).toFixed(1) + ' MB';
	}
	function loadBackups(){
		fetch(svc + '/backups').then(r=>r.json()).then(j=>{
			if(j.status==='success'){
				listEl.innerHTML = j.data.map(x=>{
					const dl = '<?php echo base_url('dbexportbackup/')?>' + encodeURIComponent(x.name);
					return '<tr>'+
					'<td><span class="badge badge-dark">'+x.name+'</span></td>'+
					'<td>'+fmtSize(x.size)+'</td>'+
					'<td>'+new Date(x.mtime).toLocaleString()+'</td>'+
					'<td>'+
					'<a class="btn btn-sm btn-primary m-r-5" href="'+dl+'" download>Download</a>'+
					'<button class="btn btn-sm btn-danger" data-del="'+encodeURIComponent(x.name)+'">Delete</button>'+
					'</td>'+
					'</tr>';
				}).join('');
			}
		});
	}

	// Delegate delete clicks (outside of render loop)
	listEl.addEventListener('click', function(ev){
		const btn = ev.target.closest('button[data-del]');
		if(!btn) return;
		const name = btn.getAttribute('data-del');
		if(!confirm('Delete '+decodeURIComponent(name)+'?')) return;
		btn.disabled = true; const oldText = btn.textContent; btn.textContent = 'Deleting...';
		fetch(svc + '/backups/' + name, { method:'DELETE' })
			.then(r=>r.json())
			.then(j=>{ if(j.status==='success'){ loadBackups(); } else { alert(j.message || 'Delete failed'); } })
			.finally(()=>{ btn.disabled=false; btn.textContent=oldText; });
	});
	document.getElementById('startNodeBackup').addEventListener('click', function(){
		progress.style.display='block';
		progress.textContent='Starting backup...';
		fetch(svc + '/backup', {method:'POST'})
			.then(r=>r.json())
			.then(j=>{
				if(j.status!=='success'){
					progress.className='alert alert-danger';
					progress.textContent='Backup failed to start: ' + (j.message || 'See server logs');
				}
			});
	});
	try{
		const es = new EventSource(svc + '/events');
		es.onmessage = function(ev){
			const e = JSON.parse(ev.data);
			if(e.type==='backup:start'){
				progress.className='alert alert-info';
				progress.textContent='Backup started: '+e.file;
			}
			if(e.type==='backup:progress'){
				progress.textContent='Writing '+e.file+' - '+fmtSize(e.bytes)+' written...';
			}
			if(e.type==='backup:complete'){
				progress.className='alert alert-success';
				progress.textContent='Backup complete: '+e.file;
				loadBackups();
			}
			if(e.type==='backup:error'){
				progress.className='alert alert-danger';
				progress.textContent='Backup error';
			}
		};
	}catch(e){}
	loadBackups();
})();
</script>