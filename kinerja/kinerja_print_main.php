<?php
function kinerja_print_main() {

	$kodeuk=arg(1);
	$topmargin = arg(2);
	$leftmargin = arg(3);
	$hal1 = arg(4);
	$tanggal = arg(5);
	$cetakttd = arg(6);
	$exportpdf = arg(7);
	
	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
	
	drupal_set_title('Cetak Kinerja');
	
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = '-skpd-kinerja_print_main-.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		//$htmlHeader = GenReportFormHeader($kodeuk, $tingkat);
		$output = getLaporankinerja_print($kodeuk,$tanggal, $cetakttd);
		//$output2= footer();
		apbd_ExportPDF('L', 'F4', $output, 'lap-kinerja_print-' . apbd_tahun(), $hal1, $topmargin, $leftmargin);

		
	} else {
		//$url = 'apbd/laporan/apbd/kinerja_print_main/'. $kodeuk . '/' . $topmargin . '/' . $hal1 . '/pdf';
		$output_form = drupal_get_form('kinerja_print_main_form');
		$output = getLaporankinerja_form($kodeuk);
		return drupal_render($output_form).$output;
		
		
	}

}

function getLaporankinerja_print($kodeuk, $tanggal, $cetakttd){
	set_time_limit(0);
	ini_set('memory_limit','640M');
	//SKPD
	$query = db_select('unitkerja', 'p');
	# get the desired fields from the database
	$query->fields('p', array('namauk','kodeuk','kodedinas', 'pimpinannama', 'pimpinanjabatan', 'pimpinannip'));
	$query->condition('p.kodeuk', $kodeuk, '=');
	# execute the query
	$results = $query->execute();
	# build the table fields
	if($results){
		foreach($results as $data) {
			$namauk = $data->namauk; 
			$kodedinas = $data->kodedinas; 
			$pimpinannama = $data->pimpinannama; 
			$pimpinanjabatan = $data->pimpinanjabatan; 
			$pimpinannip = $data->pimpinannip;
		}
	}		 
	
	$header=array();
	$rows[]=array(
		array('data' => 'LAPORAN KINERJA SATUAN PERANGKAT DAERAH', 'width' => '805px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => $namauk, 'width' => '805px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '805px','align'=>'center','style'=>'font-size:120%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '805px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$output = theme_box('', apbd_theme_table($header, $rows));
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=array();
	$header[]=array(
		array('data' => 'KODE', 'rowspan'=>'2', 'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;border-left:1px solid black;'),
		array('data' => 'PROGRAM KEGIATAN', 'rowspan'=>'2', 'colspan'=>'2', 'width' => '200px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'BELANJA', 'width' => '168px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'HASIL / KELUARAN', 'width' => '292px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'KETERANGAN', 'rowspan'=>'2', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$header[]=array(
		array('data' => 'Anggaran', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		
		array('data' => 'Rencana', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
	);
	
	$header[]=array(
		array('data' => '1', 'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '2', 'width' => '200px','align'=>'center', 'colspan'=>'2',  'style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		
		array('data' => '3', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '4', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => '5', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '6', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => '8', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	$total_anggaran = 0; $total_realisasi = 0;
	
	$result = db_query('SELECT distinct k.kodepro as pro,(select sum(l.totalp) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as anggaran,(select sum(l.realisasi) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as realisasi,p.program FROM kegiatanperubahan as k inner join program as p on k.kodepro=p.kodepro where k.totalp>0 and k.kodeuk= :kodeuk order by k.kodepro', array(':kodeuk' => $kodeuk));
	// Result is returned as a iterable object that returns a stdClass object on each iteration
	foreach ($result as $data) {
		
		$total_anggaran += $data->anggaran; $total_realisasi += $data->realisasi;	
		
		$rows[]=array(
				array('data' => $data->pro, 'width' => '45px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $data->program, 'width' => '200px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;border-right:1px solid black;'),
				
				array('data' => apbd_fn($data->anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => apbd_fn($data->realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
			
			);
		$resultk = db_query('SELECT k.kodekeg ,k.kegiatan, k.nomorkeg, k.hasilsasaran, k.keluaransasaran ,k.hasiltarget, k.hasilrealisasi, k.keluarantarget, k.keluaranrealisasi,k.totalp,k.realisasi,k.keterangan FROM kegiatanperubahan  k where  k.totalp>0 and k.kodepro= :kodepro and kodeuk= :kodeuk order by k.kodekeg', array(':kodepro' => $data->pro,':kodeuk' => $kodeuk));
		// Result is returned as a iterable object that returns a stdClass object on each iteration
		foreach ($resultk as $datak) {
			$rows[]=array(
				array('data' => $data->pro. '.'. substr($datak->kodekeg,-3), 'width' => '45px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $datak->kegiatan, 'width' => '200px', 'colspan'=>'2', 'align'=>'left','style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($datak->totalp), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => $datak->keterangan, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
				
			);
			/*$rows[]=array(
				array('data' => '', 'width' => '45px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
				array('data' => 'Dana APBD Kab. Jepara Tahun 2016', 'width' => '185px','align'=>'left','style'=>'font-style:italic; border-right:1px solid black;'),
				
				array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn2(apbd_hitungpersen($datak->totalp, $datak->realisasi)) . '%', 'width' => '84px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				array('data' => apbd_fn($datak->totalp), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
				
			);*/
			if ($datak->keluaransasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '45px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->keluaransasaran, 'width' => '185px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->keluarantarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->keluaranrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
			if ($datak->hasilsasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '45px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->hasilsasaran, 'width' => '185px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->hasiltarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->hasilrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
		}
	
	}
	
	//TOTAL
	$rows[]=array(
			array('data' => '', 'width' => '45px','align'=>'left','style'=>'border-left:1px solid black;border-top:2px solid black;'),
			array('data' => 'TOTAL', 'width' => '200px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;font-weight:bold;border-right:1px solid black;border-top:2px solid black;'),
			
			array('data' => apbd_fn($total_anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:2px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($total_realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:2px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:2px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:2px solid black;'),
			
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;border-top:2px solid black;'),
		
		);
	$rows[]=array(
			array('data' => '', 'width' => '45px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:2px solid black;'),
			array('data' => '', 'width' => '200px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;font-weight:bold;border-right:1px solid black;border-bottom:2px solid black;'),
			
			array('data' => '', 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-bottom:2px solid black;'),
			array('data' => apbd_fn2(apbd_hitungpersen($total_anggaran, $total_realisasi)) . '%', 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-bottom:2px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-bottom:2px solid black;'),
			
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:2px solid black;'),
		
		);
	
	if ($cetakttd) {
		$rows[]=array(
			array('data' => '', 'width' => '805px','align'=>'center','style'=>'border:none;'),
			
		);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>'border:none;'),
						array('data' => 'Jepara, '.$tanggal,'width' => '375px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>'border:none;'),
						array('data' => $pimpinanjabatan,'width' => '375px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '805px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '805px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '805px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '805px', 'align'=>'center','style'=>'border:none;'),
		);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>'border:none;'),
						array('data' => $pimpinannama,'width' => '375px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline'),
		);
		
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>'border:none;'),
						array('data' => 'NIP. ' . $pimpinannip,'width' => '375px', 'align'=>'center','style'=>'border:none;'),
		);
	}
	$output.=createT($header, $rows);
	//$output .= theme_box('', apbd_theme_table($header, $rows));
	//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}


function getLaporankinerja_form($kodeuk){
	
	//SKPD
	$query = db_select('unitkerja', 'p');
	# get the desired fields from the database
	$query->fields('p', array('namauk','kodeuk','kodedinas', 'pimpinannama', 'pimpinanjabatan', 'pimpinannip'));
	$query->condition('p.kodeuk', $kodeuk, '=');
	# execute the query
	$results = $query->execute();
	# build the table fields
	if($results){
		foreach($results as $data) {
			$namauk = $data->namauk; 
			$kodedinas = $data->kodedinas; 
			$pimpinannama = $data->pimpinannama; 
			$pimpinanjabatan = $data->pimpinanjabatan; 
			$pimpinannip = $data->pimpinannip;
		}
	}		
	
	$rows[]=array(
		array('data' => 'LAPORAN KINERJA SATUAN PERANGKAT DAERAH', 'colspan'=>'8', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => $namauk, 'colspan'=>'8', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(),'colspan'=>'8',  'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'colspan'=>'8', 'width' => '875px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'colspan'=>'8', 'width' => '875px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'KODE', 'rowspan'=>'2', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;border-left:1px solid black;'),
		array('data' => 'PROGRAM KEGIATAN', 'rowspan'=>'2', 'colspan'=>'2', 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' => 'BELANJA','colspan'=>'2',  'width' => '168px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'HASIL / KELUARAN', 'colspan'=>'2', 'width' => '292px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'KETERANGAN', 'rowspan'=>'2', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	
	$rows[]=array(
		array('data' => 'Anggaran', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		
		array('data' => 'Rencana', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Realisasi', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
	);
	
	$rows[]=array(
		array('data' => '1', 'width' => '50px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '2', 'width' => '265px','align'=>'center', 'colspan'=>'2',  'style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
		
		array('data' => '3', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '4', 'width' => '84px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => '5', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '6', 'width' => '146px','align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => '8', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;font-weight:bold;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	$total_anggaran = 0; $total_realisasi = 0;
	
	$result = db_query('SELECT distinct k.kodepro as pro,(select sum(l.totalp) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as anggaran,(select sum(l.realisasi) from kegiatanperubahan as l where l.kodepro=pro and l.kodeuk= :kodeuk) as realisasi,p.program FROM kegiatanperubahan as k inner join program as p on k.kodepro=p.kodepro where k.totalp>0 and k.kodeuk= :kodeuk order by k.kodepro', array(':kodeuk' => $kodeuk));
	// Result is returned as a iterable object that returns a stdClass object on each iteration
	foreach ($result as $data) {
		
		$total_anggaran += $data->anggaran; $total_realisasi += $data->realisasi;	
		
		$rows[]=array(
				array('data' => $data->pro, 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $data->program, 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;border-right:1px solid black;'),
				
				array('data' => apbd_fn($data->anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => apbd_fn($data->realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
			
			);
		$resultk = db_query('SELECT k.kodekeg ,k.kegiatan, k.nomorkeg, k.hasilsasaran, k.keluaransasaran ,k.hasiltarget, k.hasilrealisasi, k.keluarantarget, k.keluaranrealisasi,k.totalp,k.realisasi,k.keterangan FROM kegiatanperubahan  k where  k.totalp>0 and k.kodepro= :kodepro and kodeuk= :kodeuk order by k.kodekeg', array(':kodepro' => $data->pro,':kodeuk' => $kodeuk));
		// Result is returned as a iterable object that returns a stdClass object on each iteration
		foreach ($resultk as $datak) {
			$rows[]=array(
				array('data' => $data->pro. '.'. substr($datak->kodekeg,-3), 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => $datak->kegiatan, 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($datak->totalp), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-right:1px solid black;'),
				
				array('data' => $datak->keterangan, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
				
			);
			/*$rows[]=array(
				array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
				array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
				array('data' => 'Dana APBD Kabupaten Jepara Tahun 2016', 'width' => '250px','align'=>'left','style'=>'font-style:italic; border-right:1px solid black;'),
				
				array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($datak->totalp), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				array('data' => apbd_fn($datak->realisasi), 'width' => '146px','align'=>'right','style'=>'font-style:italic;border-right:1px solid black;'),
				
				array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
				
			);*/
			if ($datak->keluaransasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->keluaransasaran, 'width' => '250px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->keluarantarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->keluaranrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
			if ($datak->hasilsasaran!='') {
				$rows[]=array(
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
					array('data' => '-', 'width' => '15px','align'=>'center','style'=>'border:none;'),
					array('data' => $datak->hasilsasaran, 'width' => '250px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => '', 'width' => '84px','align'=>'right','style'=>'border-right:1px solid black;'),
					array('data' => $datak->hasiltarget, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					array('data' => $datak->hasilrealisasi, 'width' => '146px','align'=>'left','style'=>'font-style:italic;border-right:1px solid black;'),
					
					array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;'),
					
				);
			}
		}
	
	}
	
	//TOTAL
	$rows[]=array(
			array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'TOTAL', 'width' => '265px', 'colspan'=>'2', 'align'=>'left','style'=>'font-weight:bold;font-weight:bold;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			
			array('data' => apbd_fn($total_anggaran), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($total_realisasi), 'width' => '84px','align'=>'right','style'=>'font-weight:bold;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '146px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
			
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		
		);
	
	
	//$output .= theme_box('', apbd_theme_table($rows, $rows));
	$output .= theme('table', array('rows' => $rows, 'rows' => $rows ));
	return $output;
} 
 
function kinerja_print_main_form () {
	$kodeuk = arg(1);
	$topmargin = arg(2);
	$leftmargin = arg(3);
	$hal1 = arg(4);
	$tanggal=arg(5);
	$cetakttd = arg(6);
	
	if ($topmargin=='') $topmargin = '10';
	if ($leftmargin=='') $leftmargin = '23';
	if ($hal1=='') $hal1 = '1';
	if ($tanggal=='') $tanggal = date('j F Y');
	if ($cetakttd=='') {
		if (isUserSKPD())
			$cetakttd = '1';
		else
			$cetakttd = '0';
	}
	
	$form['kodeuk']= array(
		'#type'         => 'value', 
		'#value'=> $kodeuk, 
	);

	$form['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
		'#attributes' => array('class' => array('btn btn-primary btn-sm pull-right')),
	);	
	$form['topmargin']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Margin Atas', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Margin atas laporan saat dicetak', 
		'#maxlength'    => 10, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $topmargin, 
	);
	$form['leftmargin']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Margin Kiri', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Margin kiri laporan saat dicetak', 
		'#maxlength'    => 10, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $leftmargin, 
	);	// 
	$form['hal1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Halaman #1', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Halaman #1 dari laporan, isikan 9999 bila menghendaki agar nomor halaman tidak muncul', 
		'#maxlength'    => 10, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $hal1, 
	);

	$form['cetakttd'] = array(
		'#type' => 'checkbox', 
		'#title' => t('Cetak Tandatangan'),
		'#default_value'=> $cetakttd, 
	);	
	$form['tanggal']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tanggal', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => '', 
		'#maxlength'    => 50, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $tanggal, 
	);
	
	return $form;
}

function kinerja_print_main_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tanggal = $form_state['values']['tanggal'];
	$topmargin = $form_state['values']['topmargin'];
	$leftmargin = $form_state['values']['leftmargin'];
	$cetakttd = $form_state['values']['cetakttd'];
	$hal1 = $form_state['values']['hal1'];

	$uri = 'kinerjaprint/' . arg(1) . '/' . $topmargin . '/' . $leftmargin . '/' . $hal1 . '/' . $tanggal . '/' . $cetakttd . '/pdf';
	
	drupal_goto($uri);
	
}
?>