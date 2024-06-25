<link href="./embeds/noto-sans.css" rel="stylesheet">
<link href="./embeds/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="./styles.css">
<link rel="stylesheet" href="./embeds/bootstrap-icons.min.css">
<script src="./embeds/jquery-3.7.1.min.js"></script>
<script>
    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };
</script>