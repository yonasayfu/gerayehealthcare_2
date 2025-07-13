/**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Adds a nullable and unique 'fayda_id' column after the 'full_name' column
            $table->string('fayda_id')->nullable()->unique()->after('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('fayda_id');
        });
    }