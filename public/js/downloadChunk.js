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
        this.cancelDownload = false;
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

    async download(start = 0) {
        this.cancelDownload = false;
        try {
            let metaResponse = await this.downloadMeta(start);
            if (metaResponse) {
                console.log(metaResponse);
                alert("دانلود با خطا مواجه شد. لطفا مجددا تلاش کنید.");

                return {
                    type: "error",
                    fileMeta: this.fileMeta,
                    chunkNumber: metaResponse.errorOffset,
                    byteArraysFile: this.byteArraysFile,
                };
            }
        } catch (e) {
            alert("دانلود با خطا مواجه شد. لطفا مجددا تلاش کنید.");

            return {
                type: "error",
                fileMeta: this.fileMeta,
                chunkNumber: e.errorOffset,
                byteArraysFile: this.byteArraysFile,
            };
        }

        const fileMeta = this.fileMeta;
        const SIZE = fileMeta.size;
        var partCount = fileMeta.partCount;
        var hashCode = fileMeta.hashCode;
        var fileType = fileMeta.mimeType;
        for (let i = start; i < partCount; i++) {
            if (!this.cancelDownload) {
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
                    return {
                        type: "error",
                        fileMeta: fileMeta,
                        chunkNumber: e.offset,
                        byteArraysFile: this.byteArraysFile,
                    };
                    break;
                }
            } else {
                return {
                    type: "cancel",
                };
            }
        }

        var blob = new Blob([new Uint8Array(this.byteArraysFile)], {
            type: fileType,
        });
        var imageUrl = URL.createObjectURL(blob);
        console.log("bytearr", this.byteArraysFile);
        return {
            type: this.downloadError ? "error" : "success",
            url: imageUrl,
            name: fileMeta.name,
        };
    }
    async handleCancelDownload() {
        this.file = null;
        this.hashCode = null;
        this.fileMeta = null;
        this.byteArraysFile = [];
        this.bytesDownloaded = 0;
        this.downloadPercent = 0;
        this.downloadError = false;
        this.downloadDone = false;
        this.cancelDownload = true;
    }

    async resumeDownload(chunkNumber) {
        var response = await this.download(chunkNumber);
        return response;
    }

    async downloadMeta(offset) {
        let response;
        try {
            await $.post(
                "api/fileMeta/view",
                {
                    hashCode: this.hashCode,
                },
                function (res) {
                    response = res;
                }
            );
        } catch (e) {
            return {
                errorOffset: offset,
            };
        }
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
                        reject({ offset: data.offset });
                    },
                });
                return response;
            });
        } catch (e) {
            console.log("error", e);
            throw {
                offset: data.offset,
            };
        }
    }

    percentListener() {
        return this.downloadPercent;
    }
}
