<?php 
namespace Database;

class DatabaseTools
{
  /**
   * Makes query string used to filter queries.
   * @access public
   * @param array $params List with parameters.
   * @return string Query string.
   */
  public function makeQueryString($params)
  {
    $query = array();
    $paramsReturn = array();
    foreach($params as $p => $param)
    {
      if($param > 0 && $param != '' && !is_array($param))
      {
        $paramName = str_replace('.', '_', $p);
        $query[] = $p.' = :'.$paramName;
        $paramsReturn[$paramName] = $param;
      }
      elseif(is_array($param))
      {
        if($param['type'] == 'BETWEEN' && $param['to'] > $param['from'])
        {
          $paramNameFrom = str_replace('.', '_', $p.'_from');
          $paramNameTo = str_replace('.', '_', $p.'_to');
          $query[] = $p.' BETWEEN :'.$paramNameFrom.' AND :'.$paramNameTo;
          $paramsReturn[$paramNameFrom] = $param['from'];
          $paramsReturn[$paramNameTo] = $param['to'];
        }
      }
    }
    $queryString = implode(' AND ', $query);
    return array('query' => $queryString, 'params' => $paramsReturn);
  }
}