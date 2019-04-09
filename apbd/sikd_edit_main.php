<?php

function sikd_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('sikd_edit_main_form');
	return drupal_render($output_form);// . $output;
		
}

function sikd_edit_main_form($form, &$form_state) {
   

	$bulan = arg(1);
	
	if ($bulan=='') $bulan = date('n');
	if ($bulan==1)
		$bulan = 12;
	else
		$bulan--;
	
	//drupal_set_message($bulan);
	
	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';	
	$form['bulan'] = array (
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		'#default_value' => $bulan,
	);
	$form['submit']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"> XML LRA</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	return $form;
}

function sikd_edit_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	
	//createXML_LRA($bulan);
	//updateRekening();
	
	prepare_LRA();
	
}


function createXML_LRA($bulan) {
	
$writer = new XMLWriter();  
//$writer->openURI('php://output');   
//$writer->openURI('/test.xml');   
$writer->openMemory();
$writer->startDocument('1.0','UTF-8');   
$writer->setIndent(4);   

//file_put_contents('files/xml/2017991265LRA0' . $bulan . '.xml', $writer->flush(true));
$fname = 'files/xml/2017991265LRA0' . $bulan . '.xml';
file_put_contents($fname, $writer->flush(true));
$writer->startElement('ns2:realisasiAPBDWS'); 
$writer->writeAttribute('xmlns:ns2', 'http://service.sikd.app/'); 

	$writer->writeElement('kodeSatker', '991265'); 
	$writer->writeElement('kodePemda', '11.10');
	$writer->writeElement('namaPemda', 'Kabupaten Jepara');
	$writer->writeElement('tahunAnggaran', '2017');
	$writer->writeElement('periode', $bulan);
	$writer->writeElement('kodeData', '0');
	$writer->writeElement('jenisCOA', '1');
	$writer->writeElement('statusData', '0');
	$writer->writeElement('nomorPerda', 'Tidak Ada No Perda');
	$writer->writeElement('tanggalPerda', '2017-01-01');
	$writer->writeElement('userName', '991265');
	$writer->writeElement('password', '-29-35118-9840-765865');
	$writer->writeElement('namaAplikasi', 'simKEDA');
	$writer->writeElement('pengembangAplikasi', 'Astrotama');
	
	//PENDAPATAN 
	$i = 0;
	$reskegmaster = db_query('select distinct kodeuk from jurnal where jenis=:jenis and month(tanggal)<=:bulan', array(':jenis' => 'pad', ':bulan' => $bulan));
	foreach ($reskegmaster as $datakegmaster) {
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusan); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', '000'); 
			$writer->writeElement('namaProgram', 'Non Program'); 
			$writer->writeElement('kodeKegiatan', '000000'); 
			$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 

				//REKENING
				$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.jenis=:jenis AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':jenis' => 'pad', ':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
				foreach ($resrek as $datarek) {
					
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							$writer->startElement("kodeRekenings"); 
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobyek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->realisasi); 
							
							$writer->endElement();		//END REKENING
						}	
						
					
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}		
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	db_set_active();
	
	//BELANJA
	$i=0;
	//Kegiatan
	//$reskegmaster = db_query('select distinct kodeuk, kodekeg from dokumen  where jenisdokumen in (1,2,3,4,5,7) and sp2dok=1 and kodeuk=:kodeuk and month(sp2dtgl)<=:bulan', array(':kodeuk' => '08', ':bulan' => $bulan));
 	$reskegmaster = db_query('select distinct kodeuk, kodekeg from jurnal where jenis in (:spj,:umum) and month(tanggal)<=:bulan', array(':spj' => 'spj', ':umum' => 'umum-spj', ':bulan' => $bulan));
	foreach ($reskegmaster as $datakegmaster) {
		
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatan k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			//$writer->writeElement('kodeUrusanProgram', '1.20.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			//$writer->writeElement('namaUrusanProgram', 'OTDA, PEMERINTAHAN UMUM, ADM KEUANGAN'); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			//$writer->writeElement('kodeUrusanPelaksana', '1.20.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
			//$writer->writeElement('namaUrusanPelaksana', 'OTDA, PEMERINTAHAN UMUM, ADM KEUANGAN'); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', $datakeg->kodepro); 
			$writer->writeElement('namaProgram', $datakeg->program); 
			$writer->writeElement('kodeKegiatan', substr($datakegmaster->kodekeg,-6)); 
			$writer->writeElement('namaKegiatan', $datakeg->kegiatan); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 
			//$writer->writeElement('kodeFungsi', '01'); 
			//$writer->writeElement('namaFungsi', 'Pelayana Umum'); 

				//REKENING
				$resrek = db_query('SELECT ji.kodero, sum(ji.debet-ji.kredit) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodekeg=:kodekeg AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':kodekeg' => $datakegmaster->kodekeg, ':kelompok' => '5', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
				
				foreach ($resrek as $datarek) {
					$writer->startElement("kodeRekenings"); 
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobyek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->realisasi); 
						}	
						
					$writer->endElement();		//END REKENING
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}	
	}	


$writer->endElement(); 			//ns2	

//$writer->endDocument();   
//$writer->flush(); 

file_put_contents($fname, $writer->flush(true), FILE_APPEND);
$zip = new ZipArchive();
$filename = "files/xml/2017991265LRA0" . $bulan . '.zip';

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	drupal_set_message('failed');	
   
} else {
	$zip->addFile($fname, basename($fname));
	$zip->close();
	drupal_set_message('ok');
}
drupal_goto('http://akt.simkedajepara.net/'.$filename);
}

function updateRekening() {
	
db_delete('rekeninglengkap')->execute();

$resrek = db_query('select * from rincianobyek where kodero not in (select kodero from rekeninglengkap)');
foreach ($resrek as $data) {
	db_insert('rekeninglengkap')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobyek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobyek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();	
}	

$resrek = db_query('update rekeninglengkap inner join anggaran on rekeninglengkap.kodeakunutama=anggaran.kodea set rekeninglengkap.namaakunutama=anggaran.uraian');

$resrek = db_query('update rekeninglengkap inner join kelompok on rekeninglengkap.kodeakunkelompok=kelompok.kodek set rekeninglengkap.namaakunkelompok=kelompok.uraian');

$resrek = db_query('update rekeninglengkap inner join jenis on rekeninglengkap.kodeakunjenis=jenis.kodej set rekeninglengkap.namaakunjenis=jenis.uraian');

$resrek = db_query('update rekeninglengkap inner join obyek on rekeninglengkap.kodeakunobyek=obyek.kodeo set rekeninglengkap.namaakunobyek=obyek.uraian');

}

function prepare_LRA() {

	//RESET
	db_delete('realisasi_sikd')
		->execute();

	$resuk = db_query("select kodeuk, namasingkat from unitkerja where kodeuk between '00' and '04' order by kodeuk");//$reskegmaster =

	foreach ($resuk as $datauk) {
		
		drupal_set_message($datauk->kodeuk . ' - ' . $datauk->namasingkat);
		
		//PENDAPATAN 
		$reskegmaster = db_query('select kodeuk from unitkerja where kodeuk=:kodeuk', array(':kodeuk' => $datauk->kodeuk));
		foreach ($reskegmaster as $datakegmaster) {
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
			foreach ($reskeg as $datakeg) {
			 
				//REKENING
				//$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.jenis=:jenis AND LEFT(ji.kodero,1)=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':jenis' => 'pad', ':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
				$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk GROUP BY ji.kodero', array(':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk));
				foreach ($resrek as $datarek) {
					
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {

							db_insert('realisasi_sikd')
							->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
							->values(array(
									
								'periode' => 0, 
								'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
								'namaUrusanProgram' => $datakeg->urusan, 
								'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
								'namaUrusanPelaksana' => $datakeg->urusan, 
								'kodeSKPD' => $datakegmaster->kodeuk . '00', 
								'namaSKPD' => $datakeg->namauk, 
								'kodeProgram' => '000', 
								'namaProgram' => 'Non Program', 
								'kodeKegiatan' => '000000', 
								'namaKegiatan' => 'Non Kegiatan', 
								'kodeFungsi' => $datakeg->kodef, 
								'namaFungsi' => $datakeg->fungsi, 
								'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
								'namaAkunUtama' => $datainforek->namaakunutama, 
								'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
								'namaAkunKelompok' => $datainforek->namaakunkelompok, 
								'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
								'namaAkunJenis' => $datainforek->namaakunjenis, 
								'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
								'namaAkunObjek' => $datainforek->namaakunobyek, 
								'kodeAkunRincian' => substr($datarek->kodero, -3), 
								'namaAkunRincian' => $datainforek->namaakunrincian, 
								'kodeAkunSub' => '', 
								'namaAkunSub' => '', 
								'nilaiRealisasi' => $datarek->realisasi, 
								))
							->execute();							
						}	
						
					
				}

			}		
		}


		//BELANJA
		$reskegmaster = db_query('select kodeuk, kodekeg from kegiatan where kodeuk=:kodeuk', array(':kodeuk' => $datauk->kodeuk));//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where 	
		
		foreach ($reskegmaster as $datakegmaster) {
			
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatan k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
			foreach ($reskeg as $datakeg) {
			 
				//REKENING
				$resrek = db_query('SELECT ji.kodero, sum(ji.debet-ji.kredit) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodekeg=:kodekeg AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk GROUP BY ji.kodero', array(':kodekeg' => $datakegmaster->kodekeg, ':kelompok' => '5', ':kodeuk' => $datakegmaster->kodeuk));
				
				foreach ($resrek as $datarek) {
						
					$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
					foreach ($resinforek as $datainforek) {
					
						db_insert('realisasi_sikd')
						->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
						->values(array(
								
							'periode' => 0, 
							'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
							'namaUrusanProgram' => $datakeg->urusan, 
							'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
							'namaUrusanPelaksana' => $datakeg->urusandinas, 
							'kodeSKPD' => $datakegmaster->kodeuk . '00', 
							'namaSKPD' => $datakeg->namauk, 
							'kodeProgram' => $datakeg->kodepro, 
							'namaProgram' => $datakeg->program, 
							'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
							'namaKegiatan' => $datakeg->kegiatan, 
							'kodeFungsi' => $datakeg->kodef, 
							'namaFungsi' => $datakeg->fungsi, 
							'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
							'namaAkunUtama' => $datainforek->namaakunutama, 
							'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
							'namaAkunKelompok' => $datainforek->namaakunkelompok, 
							'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
							'namaAkunJenis' => $datainforek->namaakunjenis, 
							'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
							'namaAkunObjek' => $datainforek->namaakunobyek, 
							'kodeAkunRincian' => substr($datarek->kodero, -3), 
							'namaAkunRincian' => $datainforek->namaakunrincian, 
							'kodeAkunSub' => '', 
							'namaAkunSub' => '', 
							'nilaiRealisasi' => $datarek->realisasi, 
						))
						->execute();	
					}	
						
				}

			}	
		}	



	}

}

?>