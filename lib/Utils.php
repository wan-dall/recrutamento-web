<?php
namespace Teste;

use Carbon\Carbon;

/**
 * Description of Utils
 *
 * @author Fernando Wan-Dall <fernando.wandall@gmail.com>
 */
class Utils {

    /**
     * Preenche o formulário.
     *
     * Datas serão convertidas para Carbon
     *
     * @param \WTForms\Form $form
     * @param array $valores Valores. Nome dos campos do formulário deve ser o índice do array
     * @return \WTForms\Form Formulário preenchido
     *
     * @see Carbon, WTForm
     */
    public static function PreencheForm($form, $valores){
        foreach ($form->fields as $campo){
            if (isset($valores[$campo->name]) || array_key_exists($form->name, $valores)){
                //formata datas
                if ($campo->format){
                    $campo->value = Carbon::parse($valores[$campo->name])->format('d/m/Y');
                } else {
                    $campo->data = $valores[$campo->name];
                }
            }
        }
        return $form;
    }


    /**
     * Prepara array para queries com os campos do Form.
     * Campos em branco não são repassados e devem ser removidos da query.
     *
     * Campos que não tenham valor ou default definidos devem ser removidos
     * do corpo da query
     *
     * @param \WTForms\Form $form
     *
     * @return array Array para uso em DBAL
     *
     * @see DBAL::query(), DBAL::sqlQuery(), DBAL::executeQueryBuilder(), WTForm
     */
    public static function preparaForm($form){
        $params = [];

        foreach ($form->fields as $campo){
            $like_ini = $like_fin = '';
            
            if (isset($campo->render_kw['like-ini'])){
                $like_ini = '%';
            }
            
            if (isset($campo->render_kw['like-fin'])){
                $like_fin = '%';
            }
            //Ignora campos usados apenas para controle do corpo da query
            //Ex: ordem e status;
            if (isset($campo->render_kw['ignore'])){
                continue;
            }
            //Adiciona campos que tenham valores definidos ou
            //Adiciona campos que não tenham valores mas tenham default definido
            if ($campo->data or (!$campo->data and $campo->default)){
                if (is_array($campo->data)){
                    $valor = $campo->data;
                } else {
                    $valor = $like_ini . trim(($campo->data ?: $campo->default)) . $like_fin;
                }
                $params += [$campo->name => $valor];
            }
            //Campos que não tenham valor ou default definidos devem ser removidos
            //do corpo da query;
        }
        return $params;
    }
}
