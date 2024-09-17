<?php

namespace app\shop\service\order;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\common\model\order\OrderDelivery as OrderDeliveryModel;

/**
 * 訂單匯出服務類
 */
class ExportService
{
    /**
     * 訂單匯出
     */
    public function orderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列寬
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //設定工作表標題名稱
        $sheet->setTitle('訂單明細');

        $sheet->setCellValue('A1', '訂單號');
        $sheet->setCellValue('B1', '商品資訊');
        $sheet->setCellValue('C1', '訂單總額');
        $sheet->setCellValue('D1', '優惠券抵扣');
        $sheet->setCellValue('E1', '積分抵扣');
        $sheet->setCellValue('F1', '運費金額');
        $sheet->setCellValue('G1', '後臺改價');
        $sheet->setCellValue('H1', '實付款金額');
        $sheet->setCellValue('I1', '支付方式');
        $sheet->setCellValue('J1', '下單時間');
        $sheet->setCellValue('K1', '買家');
        $sheet->setCellValue('L1', '買家留言');
        $sheet->setCellValue('M1', '配送方式');
        $sheet->setCellValue('N1', '自提門店名稱');
        $sheet->setCellValue('O1', '自提聯絡人');
        $sheet->setCellValue('P1', '自提聯絡電話');
        $sheet->setCellValue('Q1', '收貨人姓名');
        $sheet->setCellValue('R1', '聯絡電話');
        $sheet->setCellValue('S1', '收貨人地址');
        $sheet->setCellValue('T1', '物流公司');
        $sheet->setCellValue('U1', '物流單號');
        $sheet->setCellValue('V1', '付款狀態');
        $sheet->setCellValue('W1', '付款時間');
        $sheet->setCellValue('X1', '發貨狀態');
        $sheet->setCellValue('Y1', '發貨時間');
        $sheet->setCellValue('Z1', '收貨狀態');
        $sheet->setCellValue('AA1', '收貨時間');
        $sheet->setCellValue('AB1', '訂單狀態');
        $sheet->setCellValue('AC1', '微信支付交易號');
        $sheet->setCellValue('AD1', '是否已評價');
        $sheet->setCellValue('AE1', '表單資訊');
        //填充資料
        $index = 0;
        foreach ($list as $order) {
            $address = $order['address'];
            $sheet->setCellValueExplicit('A' . ($index + 2), $order['order_no'], 's');
            $sheet->setCellValue('B' . ($index + 2), $this->filterProductInfo($order));
            $sheet->setCellValue('C' . ($index + 2), $order['total_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['coupon_money']);
            $sheet->setCellValue('E' . ($index + 2), $order['points_money']);
            $sheet->setCellValue('F' . ($index + 2), $order['express_price']);
            $sheet->setCellValue('G' . ($index + 2), "{$order['update_price']['symbol']}{$order['update_price']['value']}");
            $sheet->setCellValue('H' . ($index + 2), $order['order_source'] == 80 ? round($order['pay_price'] + $order['advance']['pay_price'], 2) : $order['pay_price']);
            $sheet->setCellValue('I' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('J' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('K' . ($index + 2), $order['user']['nickName']);
            $sheet->setCellValue('L' . ($index + 2), $order['buyer_remark']);
            $sheet->setCellValue('M' . ($index + 2), $order['delivery_type']['text']);
            $sheet->setCellValue('N' . ($index + 2), !empty($order['extract_store']) ? $order['extract_store']['shop_name'] : '');
            $sheet->setCellValue('O' . ($index + 2), !empty($order['extract']) ? $order['extract']['linkman'] : '');
            $sheet->setCellValue('P' . ($index + 2), !empty($order['extract']) ? $order['extract']['phone'] : '');
            $sheet->setCellValue('Q' . ($index + 2), !empty($order['address']) ? $order['address']['name'] : '');
            $sheet->setCellValue('R' . ($index + 2), !empty($order['address']) ? $order['address']['phone'] : '');
            $sheet->setCellValue('S' . ($index + 2), $address ? $address->getFullAddress() : '');
            $sheet->setCellValue('T' . ($index + 2), $this->filterExpressNameInfo($order));
            $sheet->setCellValue('U' . ($index + 2), $this->filterExpressNoInfo($order));
            $sheet->setCellValue('V' . ($index + 2), $order['pay_status']['text']);
            $sheet->setCellValue('W' . ($index + 2), $this->filterTime($order['pay_time']));
            $sheet->setCellValue('X' . ($index + 2), $order['delivery_status']['text']);
            $sheet->setCellValue('Y' . ($index + 2), $this->filterTime($order['delivery_time']));
            $sheet->setCellValue('Z' . ($index + 2), $order['receipt_status']['text']);
            $sheet->setCellValue('AA' . ($index + 2), $this->filterTime($order['receipt_time']));
            $sheet->setCellValue('AB' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('AC' . ($index + 2), $order['transaction_id']);
            $sheet->setCellValue('AD' . ($index + 2), $order['is_comment'] ? '是' : '否');
            $sheet->setCellValue('AE' . ($index + 2), $this->filterFormInfo($order));
            $index++;
        }

        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '訂單') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 分銷訂單匯出
     */
    public function agentOrderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列寬
        $sheet->getColumnDimension('B')->setWidth(30);

        //設定工作表標題名稱
        $sheet->setTitle('分銷訂單明細');

        $sheet->setCellValue('A1', '訂單號');
        $sheet->setCellValue('B1', '商品資訊');
        $sheet->setCellValue('C1', '訂單總額');
        $sheet->setCellValue('D1', '實付款金額');
        $sheet->setCellValue('E1', '支付方式');
        $sheet->setCellValue('F1', '下單時間');
        $sheet->setCellValue('G1', '一級分銷商');
        $sheet->setCellValue('H1', '一級分銷佣金');
        $sheet->setCellValue('I1', '二級分銷商');
        $sheet->setCellValue('J1', '二級分銷佣金');
        $sheet->setCellValue('K1', '三級分銷商');
        $sheet->setCellValue('L1', '三級分銷佣金');
        $sheet->setCellValue('M1', '買家');
        $sheet->setCellValue('N1', '付款狀態');
        $sheet->setCellValue('O1', '付款時間');
        $sheet->setCellValue('P1', '發貨狀態');
        $sheet->setCellValue('Q1', '發貨時間');
        $sheet->setCellValue('R1', '收貨狀態');
        $sheet->setCellValue('S1', '收貨時間');
        $sheet->setCellValue('T1', '訂單狀態');
        $sheet->setCellValue('U1', '佣金結算');
        $sheet->setCellValue('V1', '結算時間');
        //填充資料
        $index = 0;
        foreach ($list as $agent) {
            $order = $agent['order_master'];
            $sheet->setCellValueExplicit('A' . ($index + 2), $order['order_no'], 's');
            $sheet->setCellValue('B' . ($index + 2), $this->filterProductInfo($order));
            $sheet->setCellValue('C' . ($index + 2), $order['total_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('E' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('F' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('G' . ($index + 2), $agent['agent_first'] ? $agent['agent_first']['nickName'] : '');
            $sheet->setCellValue('H' . ($index + 2), $agent['first_money']);
            $sheet->setCellValue('I' . ($index + 2), $agent['agent_second'] ? $agent['agent_second']['nickName'] : '');
            $sheet->setCellValue('J' . ($index + 2), $agent['second_money']);
            $sheet->setCellValue('K' . ($index + 2), $agent['agent_third'] ? $agent['agent_third']['nickName'] : '');
            $sheet->setCellValue('L' . ($index + 2), $agent['third_money']);
            $sheet->setCellValue('M' . ($index + 2), $order['user'] ? $order['user']['nickName'] : '');
            $sheet->setCellValue('N' . ($index + 2), $order['pay_status']['text']);
            $sheet->setCellValue('O' . ($index + 2), $this->filterTime($order['pay_time']));
            $sheet->setCellValue('P' . ($index + 2), $order['delivery_status']['text']);
            $sheet->setCellValue('Q' . ($index + 2), $this->filterTime($order['delivery_time']));
            $sheet->setCellValue('R' . ($index + 2), $order['receipt_status']['text']);
            $sheet->setCellValue('S' . ($index + 2), $this->filterTime($order['receipt_time']));
            $sheet->setCellValue('T' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('U' . ($index + 2), $agent['is_settled'] == 1 ? '已結算' : '未結算');
            $sheet->setCellValue('V' . ($index + 2), $this->filterTime($agent['settle_time']));
            $index++;
        }

        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '分銷訂單') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 提現訂單匯出
     */
    public function cashList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列寬
        $sheet->getColumnDimension('H')->setWidth(50);

        //設定工作表標題名稱
        $sheet->setTitle('分銷商提現明細');

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', '分銷商id');
        $sheet->setCellValue('C1', '分銷商姓名');
        $sheet->setCellValue('D1', '微信暱稱');
        $sheet->setCellValue('E1', '手機號');
        $sheet->setCellValue('F1', '提現金額');
        $sheet->setCellValue('G1', '提現方式');
        $sheet->setCellValue('H1', '提現資訊');
        $sheet->setCellValue('I1', '稽核狀態');
        $sheet->setCellValue('J1', '申請時間');
        $sheet->setCellValue('K1', '稽核時間');
        //填充資料
        $index = 0;
        foreach ($list as $cash) {
            $sheet->setCellValue('A' . ($index + 2), $cash['id']);
            $sheet->setCellValue('B' . ($index + 2), $cash['user_id']);
            $sheet->setCellValue('C' . ($index + 2), $cash['real_name']);
            $sheet->setCellValue('D' . ($index + 2), $cash['nickName']);
            $sheet->setCellValue('E' . ($index + 2), "\t" . $cash['mobile'] . "\t");
            $sheet->setCellValue('F' . ($index + 2), $cash['money']);
            $sheet->setCellValue('G' . ($index + 2), $cash['pay_type']['text']);
            $sheet->setCellValue('H' . ($index + 2), $this->cashInfo($cash));
            $sheet->setCellValue('I' . ($index + 2), $cash['apply_status']['text']);
            $sheet->setCellValue('J' . ($index + 2), $cash['create_time']);
            $sheet->setCellValue('K' . ($index + 2), $cash['audit_time']);
            $index++;
        }
        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '分銷商提現明細') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 餘額提現訂單匯出
     */
    public function userCashList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列寬
        $sheet->getColumnDimension('I')->setWidth(50);

        //設定工作表標題名稱
        $sheet->setTitle('餘額提現明細');

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', '使用者ID');
        $sheet->setCellValue('C1', '微信暱稱');
        $sheet->setCellValue('D1', '手機號');
        $sheet->setCellValue('E1', '提現金額');
        $sheet->setCellValue('F1', '實際到賬');
        $sheet->setCellValue('G1', '提現比例');
        $sheet->setCellValue('H1', '提現方式');
        $sheet->setCellValue('I1', '提現資訊');
        $sheet->setCellValue('J1', '稽核狀態');
        $sheet->setCellValue('K1', '申請時間');
        $sheet->setCellValue('L1', '稽核時間');
        //填充資料
        $index = 0;
        foreach ($list as $cash) {
            $sheet->setCellValue('A' . ($index + 2), $cash['id']);
            $sheet->setCellValue('B' . ($index + 2), $cash['user_id']);
            $sheet->setCellValue('C' . ($index + 2), $cash['nickName']);
            $sheet->setCellValue('D' . ($index + 2), "\t" . $cash['mobile'] . "\t");
            $sheet->setCellValue('E' . ($index + 2), $cash['money']);
            $sheet->setCellValue('F' . ($index + 2), $cash['real_money']);
            $sheet->setCellValue('G' . ($index + 2), $cash['cash_ratio'] . '%');
            $sheet->setCellValue('H' . ($index + 2), $cash['pay_type']['text']);
            $sheet->setCellValue('I' . ($index + 2), $this->cashInfo($cash));
            $sheet->setCellValue('J' . ($index + 2), $cash['apply_status']['text']);
            $sheet->setCellValue('K' . ($index + 2), $cash['create_time']);
            $sheet->setCellValue('L' . ($index + 2), $cash['audit_time']);
            $index++;
        }
        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '餘額提現明細') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 抽獎記錄匯出
     */
    public function lotteryList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列寬
        $sheet->getColumnDimension('E')->setWidth(20);

        //設定工作表標題名稱
        $sheet->setTitle('抽獎記錄明細');

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', '使用者ID');
        $sheet->setCellValue('C1', '使用者暱稱');
        $sheet->setCellValue('D1', '手機號');
        $sheet->setCellValue('E1', '中獎內容');
        $sheet->setCellValue('F1', '中獎型別');
        $sheet->setCellValue('G1', '狀態');
        $sheet->setCellValue('H1', '抽獎時間');
        //填充資料
        $index = 0;
        foreach ($list as $item) {
            $sheet->setCellValue('A' . ($index + 2), $item['record_id']);
            $sheet->setCellValue('B' . ($index + 2), $item['user_id']);
            $sheet->setCellValue('C' . ($index + 2), $item['user'] ? $item['user']['nickName'] : '');
            $sheet->setCellValue('D' . ($index + 2), $item['user'] ? "\t" . $item['user']['mobile'] . "\t" : '');
            $sheet->setCellValue('E' . ($index + 2), $item['record_name']);
            $sheet->setCellValue('F' . ($index + 2), $item['lottery_type_text']);
            $sheet->setCellValue('G' . ($index + 2), $item['status'] == 1 ? '已兌換' : '未兌換');
            $sheet->setCellValue('H' . ($index + 2), $item['create_time']);

            $index++;
        }
        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '抽獎記錄明細') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 表單記錄匯出
     */
    public function tableList($tableInfo, $list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //設定工作表標題名稱
        $sheet->setTitle('表單記錄明細');
        $dataBase = ['', '表單名稱', '使用者ID', '使用者暱稱'];
        $dataBase1 = [];
        $tableData = $tableInfo['content'] ? json_decode($tableInfo['content'], true) : '';
        if ($tableData) {
            foreach ($tableData as $item) {
                $dataBase1[] = $item['name'];
            }
        }
        $dataBase = array_merge($dataBase, $dataBase1);
        $dataBase = [$dataBase];
        $listData = [];
        foreach ($list as $key => $value) {
            $tableData = json_decode($value['content'], true);
            $listData[$key] = ['', $tableInfo['name'], $value['user_id'], $value['user'] ? $value['user']['nickName'] : ''];
            foreach ($tableData as $detail) {
                $listData[$key][] = $detail['value'];
            }
        }
        $data = array_merge($dataBase, $listData);
        // 寫入資料
        foreach ($data as $row => $columns) {
            foreach ($columns as $col => $value) {
                $sheet->setCellValueByColumnAndRow($col, $row + 1, $value);
            }
        }
        //儲存檔案
        $filename = iconv("UTF-8", "GB2312//IGNORE", '表單記錄明細') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * 格式化提現資訊
     */
    private function cashInfo($cash)
    {
        $content = '';
        if ($cash['pay_type']['value'] == 20) {
            $content .= "支付寶姓名：{$cash['alipay_name']}\n";
            $content .= "  支付寶賬號：{$cash['alipay_account']}\n";
        } elseif ($cash['pay_type']['value'] == 30) {
            $content .= "銀行名稱：{$cash['bank_name']}\n";
            $content .= "  開戶名：{$cash['bank_account']}\n";
            $content .= "  銀行卡號：{$cash['bank_card']}\n";
        }
        return $content;
    }

    /**
     * 格式化物流單號資訊
     */
    private function filterExpressNoInfo($order)
    {
        if ($order['is_single'] == 1) {
            $content = '';
            $list = (new OrderDeliveryModel)->where('order_id', '=', $order['order_id'])->select();
            foreach ($list as $item) {
                $content .= "{$item['express_no']}\n";
            }
            return $content;
        } else {
            return $order['express_no'];
        }
    }

    /**
     * 格式化物流公司名稱
     */
    private function filterExpressNameInfo($order)
    {
        if ($order['is_single'] == 1) {
            $content = '';
            $list = (new OrderDeliveryModel)->with(['express'])->where('order_id', '=', $order['order_id'])->select();
            if (count($list) > 0) {
                foreach ($list as $item) {
                    $content .= "{$item['express']['express_name']}\n";
                }
            }
            return $content;
        } else {
            return $order['express'] ? $order['express']['express_name'] : '';
        }
    }

    /**
     * 格式化商品資訊
     */
    private function filterProductInfo($order)
    {
        $content = '';
        foreach ($order['product'] as $key => $product) {
            $content .= ($key + 1) . ".商品名稱：{$product['product_name']}\n";
            !empty($product['product_attr']) && $content .= "　商品規格：{$product['product_attr']}\n";
            $content .= "　購買數量：{$product['total_num']}\n";
            $content .= "　商品總價：{$product['total_price']}元\n\n";
        }
        return $content;
    }

    /**
     * 表單資訊
     */
    private function filterFormInfo($order)
    {
        $content = '';
        if ($order['custom_form']) {
            foreach ($order['custom_form'] as $key => $form) {
                if ($form['label'] != 'img') {
                    $content .= "{$form['title']}: {$form['value']}\n";
                } else {
                    $img = "";
                    if ($form['value']) {
                        foreach ($form['value'] as $value) {
                            $img .= ',' . $value['file_path'];
                        }
                    }
                    $img = trim($img, ',');
                    $content .= "{$form['title']}: $img\n";
                }
            }
        }
        return $content;
    }

    /**
     * 日期值過濾
     */
    private function filterTime($value)
    {
        if (!$value) return '';
        return date('Y-m-d H:i:s', $value);
    }

}