<?php
/**
 * Created by PhpStorm.
 * User: r16014083
 * Date: 11/10/2018
 * Time: 15:30
 */

include 'Base.php';
class MRecette extends Base
{
    public $ALaUne = 25;
    public function getIngrRec($idr)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT NOM, QUANTITE FROM INGREDIENT I, ASSO1 A WHERE I.IDI = A.IDI AND A.IDR = ?");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        return $result;
    }

    public function getIngredient()
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT IDI FROM INGREDIENT");
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        return $result;
    }

    public function getNomIngr($idi)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT NOM FROM INGREDIENT WHERE IDI=?");
        mysqli_stmt_bind_param($query, "i", $idi);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $resultarray= mysqli_fetch_assoc($result);
        return $resultarray['NOM'];
    }

    public function getIdr($idu, $nomR)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT IDR FROM RECETTE WHERE IDU = ? AND  NOMR = ?");
        mysqli_stmt_bind_param($query, "is", $idu, $nomR);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $resultarray = mysqli_fetch_assoc($result);
        return $resultarray['IDR'];
    }

    public function getNom ($idr)
    {
        $query = mysqli_prepare($this->getDbLink(),"SELECT NOMR FROM RECETTE R WHERE R.IDR=?");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
        $result= mysqli_stmt_get_result($query);
        $resultarray= mysqli_fetch_assoc($result);
        return $resultarray['NOMR'];
    }

    public function setALaUne($idr)
    {
        $this->ALaUne = $idr;
    }

    public function getALaUne()
    {
        return $this->ALaUne;
    }

    public function getAuthor($idr)
    {
        $query = mysqli_prepare($this->getDbLink(),"SELECT PSEUDO FROM RECETTE R, USER U WHERE R.IDR=? AND R.IDU=U.IDU");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
        $result= mysqli_stmt_get_result($query);
        $resultarray= mysqli_fetch_assoc($result);
        return $resultarray['PSEUDO'];
    }

    public function getNbBurn($idr)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT * FROM BURN WHERE IDR=?");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $total = mysqli_num_rows($result);
        return $total;
    }

    public function ListeRecette()
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT IDR, NOMR FROM RECETTE ORDER BY IDR DESC");
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        return $result;
    }

    public function mesRecettes($idu)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT IDR, NOMR FROM RECETTE WHERE IDU = ? ORDER BY IDR DESC");
        mysqli_stmt_bind_param($query, "i", $idu);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        return $result;
    }

    public function searchRecette($recherche)
    {
        if (empty($recherche))
        {
            $query = mysqli_prepare($this->getDbLink(), 'SELECT IDR,NOMR FROM RECETTE ORDER BY IDR DESC');
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
        }
        else
        {
            $query = mysqli_prepare($this->getDbLink(), "SELECT * FROM RECETTE R, ASSO1 A, INGREDIENT I WHERE 
                                                                R.IDR = A.IDR AND A.IDI = I.IDI AND CONCAT(R.NOMR, R.DESCR_C, R.DESCR_L,I.NOM) LIKE '%".$recherche."%'ORDER BY R.IDR DESC");
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
        }

        return $result;
    }

    public function afficherRecette($idr)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT * FROM RECETTE WHERE IDR = ?");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
        $result= mysqli_stmt_get_result($query);
        return mysqli_fetch_assoc($result);
    }

    public function supprimerRecette($idr)
    {
        $query = mysqli_prepare($this->getDbLink(), "DELETE FROM RECETTE WHERE IDR = ?");
        mysqli_stmt_bind_param($query, "i", $idr);
        mysqli_stmt_execute($query);
    }

    public function ajouterRecette($idu, $nomr, $nbconviv, $descrc, $descrl, $etape)
    {
        $query = mysqli_prepare($this->getDbLink(), "INSERT INTO RECETTE VALUES (null,?,?,?,?,?,?,'PRIVE')");
        mysqli_stmt_bind_param($query, "isisss", $idu,$nomr, $nbconviv, $descrc, $descrl, $etape);
        mysqli_stmt_execute($query);
        return mysqli_insert_id($this->getDbLink());
    }

    public function verifBurn ($idr, $idu)
    {
        $query = mysqli_prepare($this->getDbLink(), "SELECT IDB FROM BURN WHERE IDR=? AND IDU=?");
        mysqli_stmt_bind_param($query, "ii", $idr, $idu);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $total = mysqli_num_rows($result);
        if ($total == 0)
            $bool = false;
        else
            $bool = true;
        return $bool;
    }

    public function ajouterBurn ($idr, $idu)
    {
        $query = mysqli_prepare ($this->getDbLink(), 'INSERT INTO BURN VALUES (null, ?, ?)');
        mysqli_stmt_bind_param($query, 'ii', $idu,$idr);
        mysqli_stmt_execute($query);
        if ($this->getNbBurn($idr) == 15)
            $this->setALaUne($idr);
    }

    public function ajouterIngredient($idu, $nomi)
    {
        $query = mysqli_prepare ($this->getDbLink(), 'INSERT INTO INGREDIENT VALUES (null, ?, ?)');
        mysqli_stmt_bind_param($query, 'is', $idu,$nomi);
        mysqli_stmt_execute($query);
    }

    public function ajouterAsso($idi, $quantite, $idr)
    {
        $query = mysqli_prepare ($this->getDbLink(), "INSERT INTO ASSO1 VALUES (null, ?, ?, ?)");
        mysqli_stmt_bind_param($query, 'iis', $idi, $idr, $quantite);
        mysqli_stmt_execute($query);
    }
}