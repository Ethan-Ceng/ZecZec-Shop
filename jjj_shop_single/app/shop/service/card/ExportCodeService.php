<?php

namespace app\shop\service\card;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 訂單匯出服務類
 */
class ExportCodeService
{
    public static $export_field = [
        '提貨碼', '提貨密碼', '所屬卡券', '提貨開始時間', '提貨結束時間', '提貨狀態', '作廢狀態', '建立時間'
    ];

    private static $cellValues = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

    /**
     * 訂單匯出
     */
    public function exportList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //設定工作表標題名稱
        $sheet->setTitle('提貨碼明細');
        foreach (self::$export_field as $key => $field) {
            $sheet->setCellValue(self::$cellValues[$key] . '1', $field);
            if ($field == '所屬卡券') {
                $sheet->getColumnDimension(self::$cellValues[$key])->setWidth(30);
            }
        }

        //填充資料
        $index = 0;
        foreach ($list as $item) {
            foreach (self::$export_field as $key => $field) {
                $sheet->setCellValue(self::$cellValues[$key] . ($index + 2), $this->getCellValue($item, $field));
            }
            $index++;
        }

        //儲存檔案
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '提貨碼') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

// '提貨碼', '提貨密碼','所屬卡券','提貨開始時間', '提貨結束時間', '提貨狀態', '作廢狀態', '建立時間'
    private function getCellValue($item, $field)
    {
        if ($field == '提貨碼') {
            return "\t" . $item['code_no'] . "\t";
        } else if ($field == '提貨密碼') {
            return $item['code_pwd'];
        } else if ($field == '所屬卡券') {
            return $item['card_title'];
        } else if ($field == '提貨開始時間') {
            return $this->filterTime($item['start_time']);
        } else if ($field == '提貨結束時間') {
            return $this->filterTime($item['end_time']);
        } else if ($field == '提貨狀態') {
            return $item['code_status'] == 0 ? '未提貨' : '已提貨';
        } else if ($field == '作廢狀態') {
            return $item['is_delete'] == 0 ? '正常' : '作廢';
        } else if ($field == '建立時間') {
            return $item['create_time'];
        }
        return '';
    }

    /**
     * 訂單匯出
     */
    public function orderList1($list)
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

        //填充資料
        $index = 0;
        foreach ($list as $order) {
            $address = $order['address'];
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $this->filterProductInfo($order));
            $sheet->setCellValue('C' . ($index + 2), $order['total_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['coupon_money']);
            $sheet->setCellValue('E' . ($index + 2), $order['points_money']);
            $sheet->setCellValue('F' . ($index + 2), $order['express_price']);
            $sheet->setCellValue('G' . ($index + 2), "{$order['update_price']['symbol']}{$order['update_price']['value']}");
            $sheet->setCellValue('H' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('I' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('J' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('K' . ($index + 2), $order['user']['nickName']);
            $sheet->setCellValue('L' . ($index + 2), $order['buyer_remark']);
            $sheet->setCellValue('M' . ($index + 2), $order['delivery_type']['text']);
            $sheet->setCellValue('N' . ($index + 2), !empty($order['extract_store']) ? $order['extract_store']['shop_name'] : '');
            $sheet->setCellValue('O' . ($index + 2), !empty($order['extract']) ? $order['extract']['linkman'] : '');
            $sheet->setCellValue('P' . ($index + 2), !empty($order['extract']) ? $order['extract']['phone'] : '');
            $sheet->setCellValue('Q' . ($index + 2), $order['address']['name']);
            $sheet->setCellValue('R' . ($index + 2), $order['address']['phone']);
            $sheet->setCellValue('S' . ($index + 2), $address ? $address->getFullAddress() : '');
            $sheet->setCellValue('T' . ($index + 2), $order['express']['express_name']);
            $sheet->setCellValue('U' . ($index + 2), $order['express_no']);
            $sheet->setCellValue('V' . ($index + 2), $order['pay_status']['text']);
            $sheet->setCellValue('W' . ($index + 2), $this->filterTime($order['pay_time']));
            $sheet->setCellValue('X' . ($index + 2), $order['delivery_status']['text']);
            $sheet->setCellValue('Y' . ($index + 2), $this->filterTime($order['delivery_time']));
            $sheet->setCellValue('Z' . ($index + 2), $order['receipt_status']['text']);
            $sheet->setCellValue('AA' . ($index + 2), $this->filterTime($order['receipt_time']));
            $sheet->setCellValue('AB' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('AC' . ($index + 2), $order['transaction_id']);
            $sheet->setCellValue('AD' . ($index + 2), $order['is_comment'] ? '是' : '否');
            $index++;
        }

        //儲存檔案
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '訂單') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
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
     * 格式化商品資訊
     */
    private function filterProductShortName($order)
    {
        $content = '';
        foreach ($order['product'] as $key => $product) {
            $content .= ($key + 1) . ".{$product['short_name']} * {$product['total_num']}\n";
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