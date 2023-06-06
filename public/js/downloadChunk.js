class downloadFile {
    //constructor
    constructor(hashCode) {
        this.file;
        this.hashCode = hashCode;
        this.fileMeta;
        this.byteArraysFile = [];
        this.bytesDownloaded = 0;
        this.downloadPercent = 0;
        this.downloadError = false;
        this.downloadDone = false;
        this.progressListener = null; // Event listener for progress updates
    }

    setProgressListener(listener) {
        this.progressListener = listener;
    }

    updateProgress() {
        if (this.progressListener) {
            this.progressListener(this.downloadPercent);
        }
    }
    setHashCode(hashCode) {
        this.hashCode = hashCode;
    }

    async download() {
        await this.downloadMeta();

        const BYTES_PER_CHUNK = 128 * 1024; // 256KB chunk sizes.
        const fileMeta = this.fileMeta;
        const SIZE = fileMeta.size;
        var partCount = fileMeta.partCount;
        var hashCode = fileMeta.hashCode;
        var fileType = fileMeta.mimeType;
        for (let i = 0; i < partCount; i++) {
            // let buffer = await file
            //     .slice(BYTES_PER_CHUNK * i, BYTES_PER_CHUNK * (i + 1))
            //     .arrayBuffer();
            // let typedArray = new Uint8Array(buffer);

            // let array = [...typedArray];

            // if (this.byteArraysFile.length === 0) {
            //     this.byteArraysFile = array;
            // } else {
            //     this.byteArraysFile = this.byteArraysFile.concat(array);
            // }

            const data = {
                hashCode: hashCode,
                offset: i,
            };

            try {
                await this.partDownload(data).then((resolve) => {
                    if (this.byteArraysFile.length === 0) {
                        this.byteArraysFile = JSON.parse(resolve.data);
                    } else {
                        this.byteArraysFile = this.byteArraysFile.concat(
                            JSON.parse(resolve.data)
                        );
                    }
                    this.bytesDownloaded = resolve.bytes_downloaded;
                    this.downloadPercent =
                        (resolve.bytes_downloaded / SIZE) * 100;
                    this.downloadDone = resolve.finished;
                    this.downloadError = false;
                    this.updateProgress();
                });
            } catch (e) {
                console.log("ey vay!", e);
                this.downloadError = true;
                alert("دانلود با خطا مواجه شد. لطفا مجددا تلاش کنید.");
                break;
            }
        }

        var blob = new Blob([new Uint8Array(this.byteArraysFile)], {
            type: fileType,
        });
        var imageUrl = URL.createObjectURL(blob);
        console.log("bytearr", this.byteArraysFile);
        return {
            url: imageUrl,
            name: fileMeta.name,
            // blob: blob,
            // byteArr: this.byteArraysFile,
            // fileType: fileType,
        };
    }

    async downloadMeta() {
        let response;
        await $.post(
            "api/fileMeta/view",
            {
                hashCode: this.hashCode,
            },
            function (res) {
                response = res;
            }
        );
        this.fileMeta = response;
    }

    partDownload(data) {
        try {
            return new Promise(function (resolve, reject) {
                let response;

                $.ajax({
                    url: "api/filePart/view",
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
        return this.downloadPercent;
    }
}
