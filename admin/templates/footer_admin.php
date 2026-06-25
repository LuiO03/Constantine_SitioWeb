        </main>
        
        <script src="../../js/script.js"></script>

        <!--  Datatables JS-->
        <script type="text/javascript" src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    
        <script type="text/javascript" src="https://cdn.datatables.net/2.1.8/js/dataTables.jqueryui.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous">
        </script>
        <!-- Código del DataTable -->
        <script>
            $(document).ready( function(){
                $('table').not('.no-datatable').DataTable({

                    orderClasses: false,
                    "pageLength": 15,
                    lengthMenu:[
                        [15,25,50,100],
                        [15,25,50,100]
                    ],

                    "language":{
                        "url":"https://cdn.datatables.net/plug-ins/1.13.2/i18n/es-MX.json"
                    }
                });
            });
        </script>
        
</body>
</html>