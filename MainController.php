<?php

Class MainController {

    public function __construct() {
        
        switch (@$_REQUEST['action']) {
            case 'all':
                self::getAll();
                break;
            case 'single':
                self::single();
                break;
            default:
                die('permissao bloqueada');
                break;
        }
    }

    public static function getAll() {

        foreach (self::getCatalago() as $value) {
            $obj[$value['sigla']] = Array(
                'canal' => $value,
                'obj' => self::load_file($value['sigla']),
            );
        }

        foreach ($obj as $value) {

            foreach ($value['obj'] as $single) {

                $data = date('YmdHis');

                if ($single['start_controll'] <= $data && $single['stop_controll'] >= $data) {
                    
                    $single['canal'] = $value['canal'];
                    
                    $agora_na_tv[] = Array(
                        'canal' => $value['canal'],
                        'programacao' => $single,
                    );

                    break;
                }
            }
        }

        echo json_encode($agora_na_tv);
    }

    public static function single() {
        $permisao = false;
        foreach (self::getCatalago() as $value) {

            if ($value['sigla'] == $_GET['canal']) {
                $permisao = true;
                $sigla = $value;
            }
        }

        if ($permisao == true) {

            $file = self::load_file($sigla['sigla']);

            foreach ($file as $value) {
                
                $value['canal'] = $sigla['canal'];
                
                $data = date('YmdHis');
//                $data = 20150317002200;

                if ($value['start_controll'] <= $data && $value['stop_controll'] >= $data) {

                    $all_single[] = Array(
                        'canal' => $sigla,
                        'programacao' => $value,
                    );
                }
                
                if ($value['start_controll'] > $data) {

                    $all_single[] = Array(
                        'canal' => $sigla,
                        'programacao' => $value,
                    );
                }
            }
            
            echo json_encode($all_single);
            
        } else {

            echo json_encode(Array(
                'status' => false,
                'sms' => 'não encontramos',
            ));
        }
    }

    public static function load_file($name_file) {

        if (file_exists('./xml/' . $name_file . '_xmltv.xml')) {

            $file = simplexml_load_file('./xml/' . $name_file . '_xmltv.xml');

            //echod($file);

            $i = 0;
            foreach ($file as $item) {

                if (!$item['start'] && !$item['stop']) {
                    continue;
                }

                $array[] = Array(
                    'start_controll' => self::parseHorarioControll(self::xml_attribute($item, 'start')),
                    'stop_controll' => self::parseHorarioControll(self::xml_attribute($item, 'stop')),
                    'start' => self::parseHorario(self::xml_attribute($item, 'start')),
                    'stop' => self::parseHorario(self::xml_attribute($item, 'stop')),
                    'channel' => strtolower(self::xml_attribute($item, 'channel')),
                    'titulo_pt' => self::xml_attribute($item->title, 0),
                    'titulo_original' => self::xml_attribute($item->title, 1),
                    'titulo_original' => self::xml_attribute($item->title, 1),
                    'subTitulo' => (self::xml_attribute($item->{'sub-title'}, 0) ? self::xml_attribute($item->{'sub-title'}, 0) : ''),
                    'programacaoId' => self::xml_attribute($item, 'program_id'),
                    'eventoId' => self::xml_attribute($item, 'event_id'),
                    'descricao' => self::parseDescricao(self::xml_attribute($item->desc, 0)),
                    'ano' => self::xml_attribute($item->date, 0),
                );
            }
        }

        return $array;
    }

    public static function parseDescricao($descricao) {
        
        return $descricao;

        $list = explode(' ', $descricao);
        return ($list[0] * 1);
    }
    
    public static function parseHorarioControll($horario) {

        $list = explode(' ', $horario);
        return ($list[0] * 1);
    }

    public static function parseHorario($horario) {

        $list = explode(' ', $horario);

        $ano = substr($list[0], 0, 4);
        $mes = substr($list[0], 4, 2);
        $dia = substr($list[0], 6, 2);
        $hora = substr($list[0], 8, 2);
        $min = substr($list[0], 10, 2);
        $seg = substr($list[0], 12, 2);

        return "{$hora}h{$min} {$dia}/{$mes}/{$ano}";
    }

    public static function xml_attribute($object, $attribute) {
        if (isset($object[$attribute]))
            return (string) $object[$attribute];
    }

    public function loadXml($logo) {
        $file = simplexml_load_file('./xml/' . $logo . '_xmltv.xml');
        return ($file ? $file : false);
    }

    public static function getCatalago() {

        return array(
            array('sigla' => 'cfx', 'canal' => 'FX'),
            array('sigla' => 'hfa', 'canal' => 'HBO Family'),
            array('sigla' => 'mpe', 'canal' => 'Max Prime'),
            array('sigla' => 'hpe', 'canal' => 'HBO Plus'),
            array('sigla' => 'mxe', 'canal' => 'Max'),
            array('sigla' => 'mhd', 'canal' => 'Max HD'),
            array('sigla' => 'mph', 'canal' => 'Megapix HD'),
            array('sigla' => 'map', 'canal' => 'Max Prime HD'),
            array('sigla' => 'mgm', 'canal' => 'MGM'),
            array('sigla' => 't2h', 'canal' => 'Telecine Action HD'),
            array('sigla' => 'tc6', 'canal' => 'Telecine Fun'),
            array('sigla' => 'tc1', 'canal' => 'Telecine Premium'),
            array('sigla' => 'tch', 'canal' => 'Telecine Premium HD'),
            array('sigla' => 'tc2', 'canal' => 'Telecine Action'),
            array('sigla' => 'tc3', 'canal' => 'Telecine Touch'),
            array('sigla' => 'tnh', 'canal' => 'TNT HD'),
            array('sigla' => 'tnt', 'canal' => 'TNT'),
            array('sigla' => 'sph', 'canal' => 'SPACE HD'),
            array('sigla' => 'tcm', 'canal' => 'TCM - Turner Classic Movies'),
            array('sigla' => 'spa', 'canal' => 'SPACE'),
            array('sigla' => 'hbh', 'canal' => 'HBO HD'),
            array('sigla' => 'tc5', 'canal' => 'Telecine Cult'),
            array('sigla' => 'mnx', 'canal' => 'Cinemax'),
            array('sigla' => 'hbo', 'canal' => 'HBO'),
            array('sigla' => 'hb2', 'canal' => 'HBO 2'),
            array('sigla' => 'tc4', 'canal' => 'Telecine Pipoca'),
            array('sigla' => 'hfe', 'canal' => 'HBO Signature'),
            array('sigla' => 'trh', 'canal' => 'TruTV HD'),
            array('sigla' => 'tru', 'canal' => 'TruTV'),
            array('sigla' => 'hpl', 'canal' => 'HBO Plus'),
            array('sigla' => 'fox', 'canal' => 'FOX'),
            array('sigla' => 'fli', 'canal' => 'Fox Life'),
            array('sigla' => 'usa', 'canal' => 'Universal'),
            array('sigla' => 'fhd', 'canal' => 'Fox HD'),
            array('sigla' => 'axh', 'canal' => 'AXN HD'),
            array('sigla' => 'axn', 'canal' => 'AXN'),
            array('sigla' => 'wbt', 'canal' => 'Warner Channel'),
            array('sigla' => 'tbs', 'canal' => 'TBS'),
            array('sigla' => 'tbh', 'canal' => 'TBS HD'),
            array('sigla' => 'apa', 'canal' => 'TV Aparecida'),
            array('sigla' => 'apl', 'canal' => 'Animal Planet'),
            array('sigla' => 'ban', 'canal' => 'Band'),
            array('sigla' => 'bbc', 'canal' => 'BBC World News'),
            array('sigla' => 'bem', 'canal' => 'Bem Simples'),
            array('sigla' => 'bhd', 'canal' => 'BBC HD'),
            array('sigla' => 'bih', 'canal' => 'Bio HD'),
            array('sigla' => 'bio', 'canal' => 'Biography Channel'),
            array('sigla' => 'bmg', 'canal' => 'Boomerang'),
            array('sigla' => 'cah', 'canal' => 'Cartoon Network - HD'),
            array('sigla' => 'car', 'canal' => 'Cartoon Network'),
            array('sigla' => 'cfz', 'canal' => 'Canal FX HD'),
            array('sigla' => 'chd', 'canal' => 'Cultura HD'),
            array('sigla' => 'che', 'canal' => 'Chef TV'),
            array('sigla' => 'cli', 'canal' => 'Climatempo'),
            array('sigla' => 'cnh', 'canal' => 'Canção Nova HD'),
            array('sigla' => 'cnn', 'canal' => 'CNN International'),
            array('sigla' => 'cnv', 'canal' => 'Canção Nova'),
            array('sigla' => 'dci', 'canal' => 'Discovery Civilization'),
            array('sigla' => 'dhd', 'canal' => 'Discovery HD Theater'),
            array('sigla' => 'dih', 'canal' => 'Discovery Channel HD'),
            array('sigla' => 'dik', 'canal' => 'Discovery Kids'),
            array('sigla' => 'dis', 'canal' => 'Discovery'),
            array('sigla' => 'dkh', 'canal' => 'Discovery Kids - HD'),
            array('sigla' => 'dnh', 'canal' => 'Disney Channel - HD'),
            array('sigla' => 'dny', 'canal' => 'Disney Channel'),
            array('sigla' => 'dsc', 'canal' => 'Discovery Science'),
            array('sigla' => 'dts', 'canal' => 'Discovery Turbo HD'),
            array('sigla' => 'dtu', 'canal' => 'Discovery Turbo'),
            array('sigla' => 'dxd', 'canal' => 'Disney XD'),
            array('sigla' => 'ebh', 'canal' => 'ESPN Brasil HD'),
            array('sigla' => 'esb', 'canal' => 'ESPN Brasil'),
            array('sigla' => 'esh', 'canal' => 'ESPN+ HD'),
            array('sigla' => 'esi', 'canal' => 'ESPN+  SD'),
            array('sigla' => 'esp', 'canal' => 'ESPN'),
            array('sigla' => 'esq', 'canal' => 'ESPN HD'),
            array('sigla' => 'eur', 'canal' => 'Eurochannel'),
            array('sigla' => 'fhd', 'canal' => 'Fox / NatGeo HD'),
            array('sigla' => 'fne', 'canal' => 'Fox News'),
            array('sigla' => 'fow', 'canal' => 'Fox HD'),
            array('sigla' => 'fs1', 'canal' => 'Fox Sports 2 - HD'),
            array('sigla' => 'fs2', 'canal' => 'Fox Sports 2'),
            array('sigla' => 'fsh', 'canal' => 'Fox Sports HD'),
            array('sigla' => 'fsp', 'canal' => 'Fox Sports'),
            array('sigla' => 'ftv', 'canal' => 'FashionTV Brasil'),
            array('sigla' => 'fut', 'canal' => 'Futura'),
            array('sigla' => 'gaw', 'canal' => 'Gazeta HD'),
            array('sigla' => 'gaz', 'canal' => 'Gazeta'),
            array('sigla' => 'ghg', 'canal' => '+Globosat HD'),
            array('sigla' => 'ghs', 'canal' => '+Globosat'),
            array('sigla' => 'glo', 'canal' => 'Globo News HD'),
            array('sigla' => 'gnt', 'canal' => 'GNT'),
            array('sigla' => 'gnu', 'canal' => 'GNT HD'),
            array('sigla' => 'gob', 'canal' => 'Gloob'),
            array('sigla' => 'goh', 'canal' => 'Gloob HD'),
            array('sigla' => 'hih', 'canal' => 'History'),
            array('sigla' => 'mpx', 'canal' => 'Megapix'),
            array('sigla' => 'msw', 'canal' => 'Multishow'),
            array('sigla' => 'mtb', 'canal' => 'MTV Brasil'),
            array('sigla' => 'mts', 'canal' => 'Multishow HD'),
            array('sigla' => 'mtv', 'canal' => 'MTV'),
            array('sigla' => 'ngh', 'canal' => 'NatGeo Wild HD'),
            array('sigla' => 'nic', 'canal' => 'Nickelodeon'),
            array('sigla' => 'nih', 'canal' => 'Nickelodeon HD'),
            array('sigla' => 'paq', 'canal' => 'Paramount Channel HD'),
            array('sigla' => 'par', 'canal' => 'Paramount Channel'),
            array('sigla' => 'phd', 'canal' => 'Disney Junior'),
            array('sigla' => 'poa', 'canal' => 'RBS TV'),
            array('sigla' => 'rec', 'canal' => 'Record'),
            array('sigla' => 's1h', 'canal' => 'SporTV HD'),
            array('sigla' => 's2h', 'canal' => 'SporTV2 HD'),
            array('sigla' => 's3h', 'canal' => 'SporTV3 HD'),
            array('sigla' => 'sbd', 'canal' => 'SBT'),
            array('sigla' => 'sci', 'canal' => 'SyFy'),
            array('sigla' => 'seh', 'canal' => 'Sony HD'),
            array('sigla' => 'set', 'canal' => 'Sony'),
            array('sigla' => 'shd', 'canal' => 'SBT - HD'),
            array('sigla' => 'sp2', 'canal' => 'SporTV2'),
            array('sigla' => 'sp3', 'canal' => 'SporTV3'),
            array('sigla' => 'spi', 'canal' => 'Esporte Interativo'),
            array('sigla' => 'spo', 'canal' => 'SporTV'),
            array('sigla' => 'sup', 'canal' => 'National Geographic'),
            array('sigla' => 'suq', 'canal' => 'NAT GEO HD'),
            array('sigla' => 't2h', 'canal' => 'Telecine Action HD'),
            array('sigla' => 't3h', 'canal' => 'Telecine Touch HD'),
            array('sigla' => 't4h', 'canal' => 'Telecine Pipoca HD'),
            array('sigla' => 't5h', 'canal' => 'Telecine Cult HD'),
            array('sigla' => 't6h', 'canal' => 'Telecine Fun HD'),
            array('sigla' => 'tlh', 'canal' => 'TLC HD'),
            array('sigla' => 'trv', 'canal' => 'TLC'),
            array('sigla' => 'tm1', 'canal' => 'ESPN2'),
            array('sigla' => 'tm2', 'canal' => 'Nasa'),
            array('sigla' => 'tm3', 'canal' => 'NBC'),
            array('sigla' => 'tm4', 'canal' => 'Travel Channel'),
            array('sigla' => 'tm5', 'canal' => 'Speed'),
            array('sigla' => 'tm6', 'canal' => 'Arte'),
            array('sigla' => 'tm7', 'canal' => 'Red Bull TV'),
            array('sigla' => 'tm8', 'canal' => 'NFL'),
            array('sigla' => 'tm9', 'canal' => 'Fox Sports Tim'),
            array('sigla' => 'toc', 'canal' => 'Tooncast'),
            array('sigla' => 'usa', 'canal' => 'Universal Channel'),
            array('sigla' => 'ush', 'canal' => 'Universal Channel HD'),
            array('sigla' => 'viv', 'canal' => 'Viva'),
            array('sigla' => 'vvh', 'canal' => 'Viva HD'),
            array('sigla' => 'wbh', 'canal' => 'Warner HD'),
            array('sigla' => 'woo', 'canal' => 'Woohoo'),
        );
    }

}
