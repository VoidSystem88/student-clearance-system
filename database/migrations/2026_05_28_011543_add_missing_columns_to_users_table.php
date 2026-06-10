public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'birthdate')) {
            $table->date('birthdate')->nullable()->after('email');
        }
        if (!Schema::hasColumn('users', 'course')) {
            $table->string('course')->nullable()->after('birthdate');
        }
        if (!Schema::hasColumn('users', 'year_level')) {
            $table->string('year_level')->nullable()->after('course');
        }
        if (!Schema::hasColumn('users', 'course_year')) {
            $table->string('course_year')->nullable()->after('year_level');
        }
    });
}