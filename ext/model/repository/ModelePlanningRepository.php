²<?php
class ModelePlanningRepository extends Repository {
    protected $table_name = 'modele_planning';
    protected $fields = array(
        'id'            => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'jour'          => array('type' => '%s', 'nullable' => false),
        'heure_debut'   => array('type' => '%s', 'nullable' => true),
        'heure_fin'     => array('type' => '%s', 'nullable' => true),
        'id_matiere'    => array('type' => '%i', 'nullable' => false),
        'id_enseignant' => array('type' => '%i', 'nullable' => true),
        'id_classe'     => array('type' => '%i', 'nullable' => false),
        'id_periode'    => array('type' => '%i', 'nullable' => false),
    );
    
    public function search($filters = array(), $options = array()) {
        $whereEnseignant = $whereEleve = $whereClasse = '';
        if (isset($filters['id_enseignant'])) {
            $whereEnseignant = ' and mp.id_enseignant=' . $filters['id_enseignant'];
        }
        if (isset($filters['id_eleve'])) {
            $whereClasse = ' and mp.id_classe=(select id_classe from eleves_classes where id_eleve=' . $filters['id_eleve'] . ')';
        }
        if (isset($filters['id_classe'])) {
            $whereClasse = ' and mp.id_classe=' . $filters['id_classe'];
        }
        if (isset($filters['id_periode'])) {
            $whereClasse = ' and mp.id_periode=' . $filters['id_periode'];
        }
        
        $demands = 'and o.etat="validée"';
        if (array_key_exists('viewer_type', $filters)) {
            if ($filters['viewer_type'] == 'superviseur') {
                $demands = '';
            } else if ($filters['viewer_type'] == 'enseignant') {
                $demands = 'and (mp.id_enseignant=' . $_SESSION['user']['id'] . ' or o.etat="validée")';
            }
        }
        
        $reported = $this->link->fetchAll('
            select 
                date_origine as new,
                o.etat,
                mp.id,
                o.id as id_operation,
                date_format(date_report, "%w") - 1 as jour,
                case date_format(date_report, "%w")
                    when 0 then "dimanche"
                    when 1 then "lundi"
                    when 2 then "mardi"
                    when 3 then "mercredi"
                    when 4 then "jeudi"
                    when 5 then "vendredi"
                    when 6 then "samedi"
                end as jour_libelle,
                time(date_report) as heure_debut,
                addtime(time(date_report), timediff(mp.heure_fin, mp.heure_debut)) as heure_fin,
                m.nom as matiere,
                m.id as id_matiere,
                c.libelle as classe,
                c.id as id_classe,
                concat(u.civility, " ", u.nom) as enseignant,
                u.id as id_enseignant
            from
                operations o, matieres m, classes c, modele_planning mp
            left join utilisateurs u on u.id=mp.id_enseignant
            where
                o.id_modele_planning=mp.id
                ' . $demands . '
                and o.etat!="refusée"
                and mp.id_classe=c.id
                and mp.id_matiere=m.id
                ' . (!empty($filters['start']) ? 'and (o.date_origine >= "' . date('Y-m-d', $filters['start']) . '"' : '') . '
                ' . (!empty($filters['end']) ? 'and (o.date_origine <= "' . date('Y-m-d', $filters['end']) . '"' : '') . 
                $whereEnseignant . 
                $whereClasse
        );
        
        $normal = $holidays = array();
        if (!empty($filters['start']) && !empty($filters['end'])) {
            $normal = $this->link->fetchAll('
                select 
                    mp.id,
                    mp.jour-2 as jour,
                    mp.jour as jour_libelle,
                    mp.heure_debut,
                    mp.heure_fin,
                    m.nom as matiere,
                    m.id as id_matiere,
                    c.libelle as classe,
                    c.id as id_classe,
                    concat(u.civility, " ", u.nom) as enseignant,
                    u.id as id_enseignant
                from 
                    matieres m, classes c, periodes p, modele_planning mp
                left join utilisateurs u on u.id=mp.id_enseignant
                where 
                    not exists (
                        select o.id_modele_planning
                        from operations o
                        where o.id_modele_planning=mp.id ' . $demands . '
                        and o.etat!="refusée"
                        and (o.date_origine between "' . date('Y-m-d', $filters['start']) . '" and "' . date('Y-m-d', $filters['end']) . '"))
                    and mp.id_periode=p.id
                    and (date_add("' . date('Y-m-d', $filters['start']) . '", INTERVAL (mp.jour - 2) DAY) between p.date_debut and p.date_fin)
                    and not exists (
                        select id
                        from periodes
                        where type="vacances"
                        and (date_add("' . date('Y-m-d', $filters['start']) . '", INTERVAL (mp.jour - 2) DAY) between date_debut and date_fin))
                    and mp.id_classe=c.id
                    and m.id=mp.id_matiere' . 
                    $whereEnseignant . 
                    $whereClasse
            );
            $holidays = $this->link->fetchAll('
                select
                    id,
                    unix_timestamp(date_debut) as date_debut_timestamp,
                    unix_timestamp(date_fin) as date_fin_timestamp,
                    date_format(date_debut, "%d/%m/%Y") as date_debut_f,
                    date_format(date_fin, "%d/%m/%Y") as date_fin_f
                from
                    periodes
                where
                    type="vacances"
                    and (
                        date_debut between "' . date('Y-m-d', $filters['start']) . '" and "' . date('Y-m-d', $filters['end']) . '"
                        or
                        date_fin between "' . date('Y-m-d', $filters['start']) . '" and "' . date('Y-m-d', $filters['end']) . '"
                        or
                        (date_debut <= "' . date('Y-m-d', $filters['start']) . '" and date_fin >= "' . date('Y-m-d', $filters['end']) . '")
                    )'
            );
        }
        
        $days = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
        $h = array();
        
        if (!empty($holidays)) {
            $d = $filters['start'];
            while (date('Ymd', $d) <= date('Ymd', $filters['end'])) {
                if ((date('Ymd', $d) >= date('Ymd', $holidays[0]['date_debut_timestamp'])) && (date('Ymd', $d) <= date('Ymd', $holidays[0]['date_fin_timestamp']))) {
                    $h[] = array(
                        'jour_libelle'      => $days[date('w', $d)],
                        'jour'              => date('w', $d),
                        'heure_debut'       => '08:30:00',
                        'heure_fin'         => '21:00:00',
                        'holidays'          => true
                    );
                }
                $d = strtotime(date('Y-m-d', $d) . ' +1 day');
            }
        }
        $r = array('items' => array_merge($normal, $reported, $h));
        return $r;
    }
}
?>
