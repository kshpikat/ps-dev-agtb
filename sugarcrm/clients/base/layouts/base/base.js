/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/**
 * The Base Layout that all Layouts should extend from before extending
 * {@link #View.Layout}.
 *
 * Use this controller to specify your customizations for the Base platform.
 * This should contain any special override that only applies to Base platform
 * and not to Sidecar's library.
 *
 * Any Layout in a module can skip the default fallback and extend this one
 * directly. In your `BaseModuleMyLayout` component that lives in the file
 * `modules/<module>/clients/base/layouts/my-layout/my-layout.js`, you can
 * directly extend the `BaseLayout` skipping the normal extend flow which will
 * extend automatically from `BaseMyLayout` that might live in
 * `clients/base/layouts/my-layout/my-layout.js`. Simply define your controller
 * with:
 *
 * ```
 * ({
 *     extendsFrom: 'BaseLayout',
 *     // ...
 * })
 * ```
 *
 * This controller exists to force the component to be created and not fallback
 * to the default flow (which happens when the component isn't found).
 *
 * @class View.Layouts.Base.BaseLayout
 * @alias SUGAR.App.view.layouts.BaseBaseLayout
 * @extends View.Layout
 */
({
    /**
     * The Base Layout will always clear any tooltips after `render` or `dispose`.
     */
    initialize: function() {
        this._super('initialize', arguments);
        this.on('render', app.tooltip.clear);
    }
})
