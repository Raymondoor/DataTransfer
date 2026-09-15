

## Operations
### Join
### Extract
### Modify


definately need manual for the dumbees, cuz I couldn't either tbh.
    the concepts, logic, and so on.

needs orchestration

auto create list identifier.
need two identifiers for table config. One for the system, used also for tablenames, then another one used by users to reference the table.
users can define any string, where as id will be systematic, and restricted to usable string for a table name.

proccess interface class for basically defining. Then another query builder class that runs based on the proccess
interface class for building the tables with the correct columns sest.
    do I need data type? cuz blobs and stuff may be hard to handle, in terms of performance. no constraints nor references tho.

what to do on duplication in join.
    specific method for it?

## pipeline

## 使用例
```php
// 初期化
$dt = DataTransfer::boot();
$dt->setSrcDB('sqlite:/path/to/db.sqlite'); // 元DB
$dt->setOperationalDB('sqlite:/path/to/temp/db.sqlite'); // 中間DB（作業用DB）
$dt->setTargetDB('sqlite:/path/to/final/db.sqlite'); // 最終DB。元と移行先DBが同一環境にあることは絶対ではないため、元DBから一度仮最終DBへと移行し、それを後の新規環境で元DBとして、変換無しに再移行しても良い。
$prc = $dt->processor();
// プロセス（変換手順宣言）
$p1 = $prc->from(SrcDB::table('users'))->do(ExtractOperation::extract('group')); // 'group'列のみ抽出したテーブルを作成`id`も無し
$p2 = $prc->from(SrcDB::table('users'))->do(ExtractOperation::extract(['id', 'name', 'password', 'group']));
var_dump($p2->columns); // 配列もしくはオブジェクト[id, name, password]
$p3 = $prc->from($p1)->do(DistinctOperation::distinct('groups')); // `group`のみテーブルよりユニーク抽出したテーブル作成
$p4 = $prc->from($p3)->do(RenameOperation::rename(['groups'])->to(['groups_name'])); // 後の重複回避用
$p5 = $prc->from($p4)->do(AddColumnOperation::add('groups_id')); // 列追加したテーブルを作成
$p6 = $prc->from($p5)->do(ModifyValuesOperation::modify(function($records){ // コールバック。受け取った形と同じ構造でyield
    // idに数字を割り当て
    $i = 1;
    foreach($records as $record){
        $record['groups_id'] = $i;
        $i++;
        yield $record;
        // 値の数は増減しない
    }
}));
$p7 = $prc->from($p2)->do(JoinOperation::join($p6,$p6->columns->groups_name)->on('group')->left()); // 文字列を比較し、中間テーブル同士を`JOIN`する。再帰的なものは不可能
$p8 = $prc->from($p7)->do(ExtractOperation::extract(['id','name', 'password', 'groups_id'])); // `groups_id`を含んだ結合テーブルから再度抽出。
$p9 = $prc->from($p6)->do(RenameOperation::rename(['groups_id'])->to(['id'])); // 列名の変更
$p10 = $prc->from($p9)->do(SettleOperation::settle(TrgtDB::table('groups'))); // 最終移行。列名が一致しなければエラー。データ型は内部変換or指定可能？
$p11 = $prc->from($p8)->do(SettleOperation::settle(TrgtDB::table('users'))); // レファレンスの関係でグループの後に定義。

// 実行（複数オプション）
$dt->dryrun(); // 変換が正しいかを判定
$dt->graph(); // 関係図を出力
$dt->executeMap(); // 中間テーブルの全作成を実行
$dt->execute(); // データ移行実行

/**
 * 補足
 * - 上記では11個の過程があり、そのうち`settle`しないものである9つはそれぞれテーブルが作成され、データも入力される。
 * - 各中間テーブルは基本的に`immutable`となるため、超容量が必要になる場合もある。（列は絶対`imut`、データは設定可能に？）
 * - 本来のSQLに存在するような`JOIN foo.id ON bar.id WHEREfoo.name = 'baz'`は二重プロセスとなるため、デフォルトでは用意しない。（プロセスブロックを拡張可能）
 * - コードは上から下へと定義されているが、クエリの実行自体は関係性を考慮した実行手順を取る。
 */
```