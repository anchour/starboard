import classNames from 'classnames';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {MediaUpload, MediaUploadCheck},
  components: {Button},
  i18n: {__},
} = wp;

function ImageUploader(props) {
  const ALLOWED_MEDIA_TYPES = props.allowedTypes ?? ['image'];

  const {
    label,
    mediaId,
    mediaUrl,
    mediaAlt,
    onRemove,
    onSelect,
    openButtonText,
    children,
  } = props;

  const uploader = (
    <MediaUpload
      onSelect={onSelect}
      allowedTypes={ALLOWED_MEDIA_TYPES}
      instructions={
        !mediaId
          ? __(
              'Upload an image file, pick one from your media library, or add one with a URL.',
            )
          : null
      }
      value={mediaId}
      render={({open}) => (
        <div className={classNames('flex space-x-2', {'mt-2': mediaId > 0})}>
          <Button onClick={open} variant="primary">
            {/* variant="primary" for future compatibility */}
            {openButtonText ? openButtonText : __('Open Media Library')}
          </Button>

          {mediaUrl && (
            <Button
              onClick={onRemove}
              variant={'secondary'}
              isDestructive={true}
              icon="trash">
              Remove
            </Button>
          )}
        </div>
      )}
    />
  );

  return (
    <MediaUploadCheck>
      {label && <div className="font-bold mb-4">{label}</div>}
      {children ||
        (mediaUrl && (
          <img
            src={mediaUrl}
            alt={mediaAlt}
            style={{marginBottom: '0.75rem'}}
          />
        ))}
      {uploader}
    </MediaUploadCheck>
  );
}

export default ImageUploader;
