class uploadFile {
    //constructor
    constructor(file) {
        this.file = file;
        this.fileMeta;
        this.byteArraysFile = [];
        this.bytesUploaded = 0;
        this.uploadPercent = 0;
        this.uploadError = false;
        this.uploadDone = false;
        this.progressListener = null; // Event listener for progress updates
    }

    setProgressListener(listener) {
        this.progressListener = listener;
    }

    updateProgress() {
        if (this.progressListener) {
            this.progressListener(this.uploadPercent);
        }
    }
    setFile(file) {
        this.file = file;
    }

    async upload() {
        await this.uploadMeta();
        const BYTES_PER_CHUNK = 64 * 1024; // 256KB chunk sizes.
        const SIZE = file.size;
        var partCount = this.fileMeta.partCount;
        var hashCode = this.fileMeta.hashCode;
        var fileType = this.fileMeta.mimeType;
        for (let i = 0; i < partCount; i++) {
            let buffer = await file
                .slice(BYTES_PER_CHUNK * i, BYTES_PER_CHUNK * (i + 1))
                .arrayBuffer();
            let typedArray = new Uint8Array(buffer);

            let array = [...typedArray];

            if (this.byteArraysFile.length === 0) {
                this.byteArraysFile = array;
            } else {
                this.byteArraysFile = this.byteArraysFile.concat(array);
            }

            const data = {
                hashCode: hashCode,
                offset: i,
                data: array,
                mimeType: fileType,
            };

            try {
                await this.partUpload(data).then((resolve) => {
                    this.bytesUploaded = resolve.bytes_uploaded;
                    this.uploadPercent = (resolve.bytes_uploaded / SIZE) * 100;
                    this.uploadDone = resolve.finished;
                    this.uploadError = false;
                    this.updateProgress();

                });
            } catch (e) {
                console.log("ey vay!", e);
                this.uploadError = true;
                alert("آپلود با خطا مواجه شد. لطفا مجددا تلاش کنید.");
                // setUploadError([...uploadError, e]);
                break;
            }
        }
        return this.fileMeta;
    }

    async uploadMeta() {
        let response;
        await $.post(
            "api/fileMeta/insert",
            {
                mimeType: this.file.type,
                size: this.file.size,
                meta: {
                    name: this.file.name,
                },
            },
            function (res) {
                response = res;
            }
        );
        this.fileMeta = response;
    }

    partUpload(data) {
        try {
            return new Promise(function (resolve, reject) {
                let response;

                $.ajax({
                    url: "api/filePart/insert",
                    headers: {
                        "Content-Type": "application/json",
                        accept: "application/json",
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    data: JSON.stringify(data),
                    success: function (data) {
                        response = data;
                        resolve(data);
                    },
                    error: function (e) {
                        console.log("error", e);
                        reject(e);
                    },
                });
                return response;
            });
        } catch (e) {
            console.log("error", e);
            throw e;
        }
    }

    percentListener() {
        return this.uploadPercent;
    }
}
