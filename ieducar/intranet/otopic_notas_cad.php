<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Inserir Nota" );
		$this->processoAp = "294";
	}
}

class indice extends clsCadastro
{
	var $cod_grupo;
	var $cod_membro;
	var $sequencial;
	var $nota;
	var $id_pessoa;

	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";

		$this->cod_membro = $_GET['cod_membro'];
		$this->cod_grupo = $_GET['cod_grupo'];
		$this->sequencial = $_GET['sequencial'];
		
		if($this->sequencial)
		{
			$obj = new clsNotas($this->cod_membro, false,false,false,false,$this->sequencial);
			$detalhe = $obj->detalhe();
			if($detalhe)
			{
				if($detalhe['ref_pessoa_cad'] != $this->id_pessoa)
				{
					header("Location: otopic_membro_det.php?cod_membro=$this->cod_membro&cod_grupo=$this->cod_grupo");
	
				}
				$this->nota = $detalhe['nota'];
				$retorno = "Editar";
				$this->fexcluir = true;
	
			}else 
			{
					header("Location: otopic_membro_det.php?cod_membro=$this->cod_membro&cod_grupo=$this->cod_grupo");
			}
		}
		
		$obj_moderador = new clsGrupoModerador($this->cod_membro,$this->cod_grupo);
		$detalhe_moderador = $obj_moderador->detalhe();
		$obj_grupo_pessoa = new clsGrupoPessoa($this->cod_membro,$this->cod_grupo);
		$detalhe_grupo_pessoa = $obj_grupo_pessoa->detalhe();
		
		if(!$detalhe_moderador && !$detalhe_grupo_pessoa )
		{		
			header("Location: otopic_meus_grupos_lst.php");
		}
		
		$this->url_cancelar =  "otopic_membro_det.php?cod_grupo=$this->cod_grupo&cod_membro=$this->cod_membro";
		$this->nome_url_cancelar = "Cancelar";


		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto("id_pessoa",$this->id_pessoa);
		$this->campoOculto("cod_membro",$this->cod_membro);
		$this->campoOculto("cod_grupo",$this->cod_grupo);
		$this->campoOculto("sequencial",$this->sequencial);
		$this->campoMemo("nota","Nota",$this->nota,60,5,true);
	}
	
	
	function Novo() 
	{
		$obj = new clsNotas($this->cod_membro,$this->id_pessoa,false,$this->nota);
		if($obj->cadastra())
		{
			header("Location: otopic_membro_det.php?cod_membro=$this->cod_membro&cod_grupo=$this->cod_grupo");
		}
		return false;
	}

	function Editar() 
	{
		$obj = new clsNotas($this->cod_membro,$this->id_pessoa,false,$this->nota,false,$this->sequencial);
		if($obj->edita())
		{
			header("Location: otopic_membro_det.php?cod_membro=$this->cod_membro&cod_grupo=$this->cod_grupo");
		}		

	}

	function Excluir()
	{
		$obj = new clsNotas($this->cod_membro,false,$this->id_pessoa,$this->nota,false,$this->sequencial);
		if($obj->exclui())
		{
			header("Location: otopic_membro_det.php?cod_membro=$this->cod_membro&cod_grupo=$this->cod_grupo");
		}		
		
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
