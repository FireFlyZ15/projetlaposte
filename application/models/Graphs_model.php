<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Graphs_model
 * Contient les requetes SQL qui vont permettre de gérer les graphiques
 */
class Graphs_model extends CI_Model
{
    private static $table_graph = 'graph';
    private static $table_balance = 'balance';

    /**
     * Les valeurs differente de la colonne voulus pour la génération de filtres pour la liste des graphiques
     * @param $column Colonne à analyser
     * @param null $tablejoin nom de la table pour une jointure
     * @param null $varjoin nom de la colonne dans graph pour la jointure
     * @param null $column2 nom de ma colonne dans $tablejoin pour la jointure
     * @return mixed
     */
    public function getDiffValueColumn($column, $tablejoin = NULL, $varjoin = NULL, $column2 = NULL)
    {
        $this->load->database();
        $this->db->cache_on();
        if ($column2 != NULL) {
            $this->db->select($column . " as value, " . $tablejoin . "." . $column2);

        } else {
            $this->db->select($column . " as value");
        }

        $this->db->from(self::$table_graph);
        $this->db->group_by($column);
        if ($tablejoin != NULL && $varjoin != NULL) {
            $this->db->join($tablejoin, self::$table_graph . '.' . $column . ' = ' . $tablejoin . '.' . $varjoin);
        }
        return $this->db->get()->result();
    }

    /**
     * Listes des tables qui ont au moins un graphique
     * @return mixed Résultat de la requête
     */
    public function getDiffTable()
    {
        $this->load->database();
        $this->db->cache_on();
        $this->db->select("source, database.name as source_name, database, table");
        $this->db->group_by("source, source_name, database, table");
        $this->db->order_by('source_name', 'ASC');
        $this->db->order_by('database', 'ASC');
        $this->db->order_by('table', 'ASC');

        $this->db->from(self::$table_graph);
        $this->db->join("database", self::$table_graph . '.source = database.id');
        return $this->db->get()->result();
    }

    /**
     * Sauvegarde du graphique
     * @param string $name Nom du graphique
     * @param string $type Type du graphique
     * @param string $description Description du graphique
     * @param string $script Données brut pour la génération du graphique
     * @param string $config Configuration en JSON du graphique
     * @param string $userID Identifiant du créateur du graphique
     * @param string $source Identifiant de la source de données
     * @param string $database Nom de la base de données
     * @param string $table Nom de la table
     * @param int $public Mode public?
     * @param int $live Mode Live?
     * @param string $image_name Identifiant de l'image
     * @param string $group Identifiant du groupe du graphique
     * @return mixed Résultat de la requête
     */
    public function saveGraph($name, $type, $description, $script, $config, $userID, $source, $database, $table, $public = 0, $live = 0, $image_name = "", $group = "")
    {
        $this->load->database();
        return $this->db->set('name', $name)
            ->set('type', $type)
            ->set('description', $description)
            ->set('script', $script)
            ->set('config', $config)
            ->set('user', $userID)
            ->set('source', $source)
            ->set('database', $database)
            ->set('table', $table)
            ->set('public', $public)
            ->set('live', $live)
            ->set('image_name', $image_name)
            ->set('group', $group)
            ->set('date_creation', date('Y-m-d G:i:s'))
            ->set('date_update', date('Y-m-d G:i:s'))
            ->set('date_view', date('Y-m-d G:i:s'))
            ->insert(self::$table_graph);
    }

    /**
     * Mise à jour du graphique
     * @param string $id Identifiant du graphique
     * @param string $name Nom du graphique
     * @param string $type Type du graphique
     * @param string $description Description du graphique
     * @param string $script Données brut pour la génération du graphique
     * @param string $config Configuration en JSON du graphique
     * @param string $source Identifiant de la source de données
     * @param string $database Nom de la base de données
     * @param string $table Nom de la table
     * @param int $public Mode public?
     * @param int $live Mode Live?
     * @param string $image_name Identifiant de l'image
     * @param string $group Identifiant du groupe du graphique
     * @return mixed Résultat de la requête
     */
    public function updateGraph($id, $name, $type, $description, $script, $config, $source, $database, $table, $public = 0, $live = 0, $image_name = "", $group = "")
    {
        $this->load->database();
        $data = array(
            'name' => $name,
            'type' => $type,
            'description' => $description,
            'script' => $script,
            'config' => $config,
            'source' => $source,
            'database' => $database,
            'table' => $table,
            'public' => $public,
            'live' => $live,
            'image_name' => $image_name,
            'group' => $group,
            'date_update' => date('Y-m-d G:i:s'),
            'date_view' => date('Y-m-d G:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update(self::$table_graph, $data);

    }

    /**
     * Permet d'avoir le nombre de graphiques disponible pour les filtres mise en place
     * @param string $search Filtre sur le nom du graphique
     * @param bool $all Affiche tous les graphiques sans prendre en compte le statut publique
     * @param string $userId Utilisateur qui demande la liste
     * @param array $typeGet Types des graphiques à afficher
     * @param array $tableGet Tables des craphiques à afficher
     * @param array $userGet Créateurs des graphiques à afficher
     * @param array $groupGet Groupes des graphiques à afficher
     * @param array $groupAllowed Groupes autorisés pour l'utilisateur.
     * @return int Nombre de graphique
     */
    public function countNbGraph($search = "", $all = false, $userId = "", $typeGet = [], $tableGet = [], $userGet = [], $groupGet = [], $groupAllowed = [])
    {
        $this->load->database();
        $this->load->database();
        $this->db->select("count(*) as value");
        if ($search != "") {
            $this->db->group_start();
            $this->db->like('graph.name', $search);
            $this->db->or_like('graph.type', $search);
            $this->db->group_end();
        }

        if ($all == false && $userId != "") {
            $this->db->group_start()
                ->group_start()
                ->where('graph.public', 1);
            if ($groupAllowed != []) {
                $this->db->group_start();
                $this->db->where('graph.group', "");
                $this->db->or_where('graph.group', NULL);
                foreach ($groupAllowed as $group) {
                    $this->db->or_where('graph.group', $group);
                }
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->where('graph.group', "");
                $this->db->or_where('graph.group', NULL);
                $this->db->group_end();
            }
            $this->db->group_end()
                ->or_where('graph.user', $userId)
                ->group_end();

        }

        if ($typeGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($typeGet as $type) {
                if ($nb == 0) {
                    $this->db->where('graph.type', $type);
                } else {
                    $this->db->or_where('graph.type', $type);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        if ($tableGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($tableGet as $table) {
                $tableAr = explode(".", $table);
                if (count($tableAr) == 3) {
                    if ($nb == 0) {
                        $this->db->group_start();
                    } else {
                        $this->db->or_group_start();
                    }
                    $this->db->where('graph.source', $tableAr[0]);
                    $this->db->where('graph.database', $tableAr[1]);
                    $this->db->where('graph.table', $tableAr[2]);
                    $this->db->group_end();
                }
                $nb++;
            }
            $this->db->group_end();
        }
        if ($userGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($userGet as $userForm) {
                if ($nb == 0) {
                    $this->db->where('graph.user', $userForm);
                } else {
                    $this->db->or_where('graph.user', $userForm);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        if ($groupGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($groupGet as $groupForm) {
                if ($nb == 0) {
                    $this->db->where('graph.group', $groupForm);
                } else {
                    $this->db->or_where('graph.group', $groupForm);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        $this->db->from(self::$table_graph);
        $this->db->join('user', 'graph.user = user.id');
        return $this->db->get()->result()[0]->value;
    }

    /**
     * Affiche le nombre de graphique pour chaque groupe
     * @return array Tableau de nombre de graphique pour chaque groupe
     */
    public function countGraphByGroup()
    {
        $this->load->database();
        $this->db->select("group, count(*) as nb");
        $this->db->from(self::$table_graph);
        $this->db->group_by("group");
        $list = $this->db->get()->result();
        $result = [];
        foreach ($list as $row) {
            $result[$row->group] = $row->nb;
        }
        return $result;
    }

    /**
     * Récupération de la liste de graphique
     * @param string $search Filtre sur le nom du graphique
     * @param bool $all Affiche tous les graphiques sans prendre en compte le statut publique
     * @param string $userId Utilisateur qui demande la liste
     * @param array $typeGet Types des graphiques à afficher
     * @param array $tableGet Tables des craphiques à afficher
     * @param array $userGet Créateurs des graphiques à afficher
     * @param int $nbPage Numéro de la page (8 graphiques par page)
     * @param array $groupGet Groupes des graphiques à afficher
     * @param array $groupAllowed Groupes autorisés pour l'utilisateur.
     * @return mixed Résultat de la requête
     */
    public function listGraph($search = "", $all = false, $userId = "", $typeGet = [], $tableGet = [], $userGet = [], $nbPage = 1, $groupGet = [], $groupAllowed = [], $order_name = "", $order_type = "")
    {
        $this->load->database();
        $this->db->select("graph.id,graph.name, graph.type, graph.description, graph.date_creation, graph.image_name, 
            graph.user, user.email, user.type as userType, graph.source, graph.database, graph.table, graph.public, graph.live, group.name as group, database.name as source_name");
        if ($search != "") {
            $this->db->group_start();
            $this->db->like('graph.name', $search);
            $this->db->or_like('graph.type', $search);
            $this->db->group_end();
        }

        if ($all == false && $userId != "") {
            $this->db->group_start()
                ->group_start()
                ->where('graph.public', 1);
            if ($groupAllowed != []) {
                $this->db->group_start();
                $this->db->where('graph.group', "");
                $this->db->or_where('graph.group', NULL);
                foreach ($groupAllowed as $group) {
                    $this->db->or_where('graph.group', $group);
                }
                $this->db->group_end(); 
            } else {
                $this->db->group_start();
                $this->db->where('graph.group', "");
                $this->db->or_where('graph.group', NULL);
                $this->db->group_end();
            }
            $this->db->group_end()
                ->or_where('graph.user', $userId)
                ->group_end();

        }

        if ($typeGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($typeGet as $type) {
                if ($nb == 0) {
                    $this->db->where('graph.type', $type);
                } else {
                    $this->db->or_where('graph.type', $type);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        if ($tableGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($tableGet as $table) {
                $tableAr = explode(".", $table);
                if (count($tableAr) == 3) {
                    if ($nb == 0) {
                        $this->db->group_start();
                    } else {
                        $this->db->or_group_start();
                    }
                    $this->db->where('graph.source', $tableAr[0]);
                    $this->db->where('graph.database', $tableAr[1]);
                    $this->db->where('graph.table', $tableAr[2]);
                    $this->db->group_end();
                }

                $nb++;
            }
            $this->db->group_end();
        }
        if ($userGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($userGet as $userForm) {
                if ($nb == 0) {
                    $this->db->where('graph.user', $userForm);
                } else {
                    $this->db->or_where('graph.user', $userForm);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        if ($groupGet != []) {
            $this->db->group_start();
            $nb = 0;
            foreach ($groupGet as $groupForm) {
                if ($nb == 0) {
                    $this->db->where('graph.group', $groupForm);
                } else {
                    $this->db->or_where('graph.group', $groupForm);
                }
                $nb++;
            }
            $this->db->group_end();
        }
        $this->db->from(self::$table_graph);
        $this->db->join('user', 'graph.user = user.id');
        $this->db->join('group', 'graph.group = group.id', 'left');
        $this->db->join('database', 'graph.source = database.id', 'left');
        $nbPage = 8 * ($nbPage - 1);
        $this->db->limit(8, $nbPage);
        if (in_array($order_name, ['id', 'date_update', 'date_view']) && in_array($order_type, ['ASC', 'DESC'])) {
            $this->db->order_by($order_name, $order_type);
        } else {
            $this->db->order_by('id', 'ASC');
        }

        return $this->db->get()->result();
    }

    /**
     * Récupération du graphique
     * @param $id Identifiant du graphique
     * @return mixed Résultat de la requête
     */
    public function getGraph($id)
    {
        //Mise à jour de la date de dernier visionnage pour le trie de la liste des graphiques
        $this->updateDateViewGraph($id);
        $this->load->database();
        return $this->db->get_where(self::$table_graph, array('id' => $id))->row();
    }

    /**
     * Duplication d'un graphique
     * @param $id Identifiant du graphique
     * @param $user Identifiant de l'utilisateur qui fait cette action
     * @param $image_name Identifiant de la nouvelle image
     * @param $config Nouveau json de configuration du graphique
     * @return mixed Résultat de la requête
     */
    public function duplicateGraph($id, $user, $image_name, $config)
    {
        $this->load->database();

        $sql = "INSERT INTO " . self::$table_graph . " (`name`, `type`, `description`, `script`, `date_creation`, `date_update`, `date_view`, `config`, `image_name`, `user`, `source`, `database`, `table`, `public`, `live`, `group`) 
        SELECT CONCAT(name, \" copie\") as name, `type`, `description`, '', NOW(),NOW(),NOW(), '" . $config . "', '" . $image_name . "', '" . $user->id . "', `source`, `database`, `table`, `public`, `live`, `group` FROM " . self::$table_graph . " where id = ?";
        return $this->db->query($sql, array($id));

    }

    /**
     * Suppression d'un graphique
     * @param $id Identifiant du graphique
     */
    public function deleteGraph($id)
    {
        $this->load->database();
        $this->db->where('id', $id)
            ->delete(self::$table_graph);

    }

    /**
     * Mise à jour de la date de dernier visionnage pour le trie de la liste des graphiques
     * @param $id Identifiant du graphique
     * @return
     */
    public function updateDateViewGraph($id)
    {
        $this->load->database();
        $data = array(
            'date_view' => date('Y-m-d G:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update(self::$table_graph, $data);

    }
}
