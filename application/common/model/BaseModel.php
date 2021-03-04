<?php
namespace app\common\model;

use think\Db;
use think\Model;
/**
 * 基础model
 */
class BaseModel extends Model {

    const NO = 0;
    const YES = 1;

    // 设置返回数据结果的类型
    protected $resultSetType = 'collection';

    /**
     * 添加数据
     * @param  array $data  添加的数据
     * @return int          插入的行数
     */
    public function addData($data){
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v);
            }
        }
        $result = $this->isUpdate(false)->save($data);
        return $result;
    }

    /**
     * 修改数据
     * @param   array   $map    where语句数组形式
     * @param   array   $data   数据
     * @return  mixed           受影响的行数
     */
    public function editData($map, $data){
        // 去除键值首位空格
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v);
            }
        }
        $result = $this->save($data, $map);
        if ($result === 0) {
            $result = true;
        }
        return $result;
    }

    /**
     * 删除数据
     * @param   array   $map    where语句数组形式
     * @return  boolean         操作是否成功
     */
    public function deleteData($map){
        if (empty($map)) {
            die('where为空的危险操作');
        }
        $result = $this->where($map)->delete();
        return $result;
    }

    /**
     * 数据排序
     * @param  array $data   数据源
     * @param  string $id    主键
     * @param  string $order 排序字段
     * @return boolean       操作是否成功
     */
    public function orderData($data,$id='id',$order='ord'){
        foreach ($data as $k => $v) {
            $v=empty($v) ? null : $v;
            $this->where(array($id=>$k))->save(array($order=>$v));
        }
        return true;
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$map,$order='',$limit=10,$field=''){
        $count=$model
            ->where($map)
            ->count();
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->field($field)
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
        );
        return $data;
    }
}