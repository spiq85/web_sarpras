    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateBarangTable extends Migration
    {
        public function up()
        {
            Schema::create('barang', function (Blueprint $table) {
                $table->id('id_barang');
                $table->foreignId('id_category')->constrained('kategori_barang', 'id_category')->onDelete('cascade');
                $table->string('kode_barang')->unique();
                $table->string('nama_barang');
                $table->integer('stock');
                $table->integer('stock_dipinjam')->default(0);
                $table->string('brand')->nullable();
                $table->enum('status', ['tersedia','dipinjam'])->nullable();
                $table->enum('kondisi_barang', ['baik', 'rusak', 'dll'])->nullable();
                $table->string('gambar_barang')->nullable();
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('barang');
        }
    }
